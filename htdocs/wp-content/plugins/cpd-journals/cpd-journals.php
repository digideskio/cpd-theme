<?php
/**
 * @package CPD-Journals
 */
/*
Plugin Name: CPD-Journals
Plugin URI: http://czndigital.com/wp/cpd-journals
Description: A plug-in to manage and support cohorts of people through a continuous professional development process by providing a platform for them to keep journals of their CPD activities.
Version: 0.3
Author: Saul Cozens
Author URI: http://saulcozens.co.uk
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define('CPD_JOURNAL_VERSION', '0.2');
define('CPD_JOURNAL_PLUGIN_URL', plugin_dir_url( __FILE__ )); // wrong diddly wrong


if(!class_exists('cpd_journal')) {
	class cpd_journal {

		var $notifications;
		var $relationship_table;

		function __construct() {
			// registration hook
			register_activation_hook( __FILE__, array($this,'on_activate' ));

			$this->_init();
		}
		function _init() {
			global $wpdb;

			$this->posts_table="{$wpdb->base_prefix}cpd_posts";
			$this->relationship_table="{$wpdb->base_prefix}cpd_relationships";

			wp_register_style('cpd_journal',plugins_url( 'cpd-journals.css' , __FILE__ ));
			wp_enqueue_style('cpd_journal');

			wp_register_script('cpd_journal',plugins_url( 'cpd-journals.js' , __FILE__ ), 'jquery');
			wp_enqueue_script('cpd_journal');

			add_action('set_user_role', array($this, 'set_role_metadata'), 10, 2);
			add_filter('editable_roles', array($this, 'editable_roles'));
			add_filter('user_has_cap', array($this,'user_has_cap'), 10, 3);
			//add_filter('option_'.$wpdb->prefix . 'user_roles', array($this, 'override_blog_user_roles'));

			// add new roles to the list both are administrators
			// we don't do this on_activate as we want these roles and capabilities enforced in every blog, not just the network one
			add_role('supervisor', 'Supervisor', get_role('administrator')->capabilities);
			add_role('participant', 'Participant', get_role('administrator')->capabilities);
			$role = get_role( 'participant' );
			$role->remove_cap( 'edit_others_posts' );
			$role->remove_cap( 'edit_others_pages' );
			$role->remove_cap( 'delete_others_posts' );
			$role->remove_cap( 'delete_others_pages' );

			add_action('wp_network_dashboard_setup', array($this,"add_network_dashboard_widgets"));
			add_action('wp_dashboard_setup', array($this,"add_dashboard_widgets"));
			add_action('admin_notices', array($this, 'user_management_notice'));

			add_action('network_admin_menu', array($this, 'add_network_admin_menu'));
			add_action('network_admin_edit_update_cpd_settings', array($this, 'update_cpd_settings'));
			add_action('admin_menu', array($this,'cpdpp_remove_admin_menus')); // If the user is a participant, limit the options they see in the menu


			add_action('wp_ajax_posts_in_week', array($this,'posts_in_week'));
			add_action('wp_ajax_posts_by_user', array($this,'posts_by_user'));


			add_action('edit_user_profile', array($this, 'edit_cpd_profile'));
			add_action('show_user_profile', array($this, 'edit_cpd_profile')); // needed for admin to edit own cpd profile
			add_action('edit_user_profile_update', array($this, 'update_cpd_profile'));
			add_action('personal_options_update', array($this, 'update_cpd_profile'));// needed for admin to edit own cpd profile
			add_action('wpmu_new_user', array($this,'wpmu_new_user'));

			add_action('save_post', array($this, 'update_cpd_posts'));
			add_action('save_post', array($this,'cpdje_send_mail_on_update')); // Send mail when a post is published or updated
			add_filter('wp_mail_content_type', array($this,'cpdje_set_content_type')); // Set the mail types to HTML

			/* admin user list/table hooks */
			add_action('manage_users_custom_column', array($this,'cpd_role_column'), 15, 3);
			add_filter('manage_users-network_sortable_columns', array($this,'add_cpd_role_column_sort'));
			add_filter('views_users-network', array($this,'add_cpd_role_views'));
			add_filter('wpmu_users_columns', array($this,'add_cpd_role_column'), 15, 1); 
			add_action('pre_user_query', array($this,'filter_and_order_by_cpd_column'));
			add_action('cpd_unassigned_users_email', array($this, 'unassigned_users_email'));

		}

		function on_activate() {
			global $wpdb;

			// this table keeps a record of the relationsips between participants and supervisors
			$sql="CREATE TABLE IF NOT EXISTS {$this->relationship_table} (
				supervisor_id bigint(20) NOT NULL,
				participant_id bigint(20) NOT NULL,
				INDEX supervisor_id_FI (supervisor_id),
				INDEX participant_id_FI (participant_id),
				PRIMARY KEY relationship (supervisor_id,participant_id))";
			$wpdb->query($sql);

			// we keep a record of all of the posts on any blog in this table to make reporting across all blogs easier
			// otherwise we'd have to run lot's of queries to get stuff like post titles and dates.
			$sql="CREATE TABLE IF NOT EXISTS {$this->posts_table} (
				cpd_post_id BIGINT(20) NOT NULL AUTO_INCREMENT,
				user_id BIGINT(20) NOT NULL,
				post_id BIGINT(20) NOT NULL,
				blog_id BIGINT(20) NOT NULL,
				site_id BIGINT(20) NOT NULL,
				post_date DATETIME,
				post_status VARCHAR(20),
				post_title TEXT,
				guid VARCHAR(255),
				INDEX user_id_FI (user_id),
				UNIQUE KEY  (post_id,site_id,blog_id),
				PRIMARY KEY post_id_PI (cpd_post_id))";
			$wpdb->query($sql);


			// if it's not already scheduled - setup the regular email
			if(!wp_next_scheduled('cpd_unassigned_users_email')) {
				wp_schedule_event(strtotime("02:00am"), 'daily', 'cpd_unassigned_users_email');
			}
		}


		function wpmu_new_user($user_id){
			// this is a bit hacky - we need to stop the redirect to the user-new.php page happening.
			// the only way is to recreate the password and send the user notification ourselves.
			// then we can forward to the user profile where the admin can set their supervisors
			// (if they are a participant) or participants (if they are a supervisor)
			if ($user_id ) {

				// all new users get access (as a subscriber) to the default blog
				add_user_to_blog(BLOG_ID_CURRENT_SITE, $user_id, 'subscriber');


				// create a new password, because we need to send it and we don't have access to the one already generated 
				$password= wp_generate_password( 12, false);
				wp_set_password($password, $user_id);


				wp_new_user_notification( $user_id, $password );
				wp_redirect( add_query_arg( array('user_id' => $user_id), network_admin_url('user-edit.php#cpd_profile' )) );
				exit;
			}


		}

		function user_management_notice(){
			if(is_super_admin() && !is_network_admin()) {
				echo '<div class="updated">';
				echo '<p>To add, remove or manage CPD supervisors or participants, please use the <a href="'.network_admin_url().'">Network Dashboard</a></p>';
				echo '</div>';
			}
		}

		function add_network_admin_menu() {
			add_submenu_page('settings.php','CPD settings', 'CPD settings', 'manage_network_options', 'cpd_settings', array($this, 'cpd_settings_page'));

		}

		function cpd_settings_page(){ 
			?>
			<div class="wrap">  
				<h2>CPD Settings</h2>  
				<form method="post" action="edit.php?action=update_cpd_settings">  
					<?php wp_nonce_field('update_cpd_settings') ?>  
					<p><strong>New participant blog default options</strong><br />  
						<textarea name="cpd_new_blog_options" cols="40" rows="8"><?php echo get_option('cpd_new_blog_options'); ?></textarea><br />
						These options are used to set up a new blog for a CPD participant. Enter any valid blog meta name/value pairs.  Enter one pair per line separated by any whitespace characters.
					</p>  
					<p><input type="submit" name="Submit" value="Update" /></p>  
					<input type="hidden" name="action" value="update" />  
					<input type="hidden" name="page_options" value="cpd_new_blog_options" />  
				</form>  
			</div> 
		<?php
		}

		function update_cpd_settings(){

			check_admin_referer('update_cpd_settings');
			if(!current_user_can('manage_network_options')) 
				wp_die('You do not have permission to do this - please leave quietly');

			update_option('cpd_new_blog_options', $_POST['cpd_new_blog_options']);

			$cpd_settings=get_option('cpd_new_blog_options');
			$cpd_settings=preg_replace('/[\n\r]+/', '&', $cpd_settings);
			$cpd_settings=preg_replace('/[\s\:]+/', '=', $cpd_settings);
			parse_str($cpd_settings, $options);



			wp_redirect(add_query_arg(array('page' => 'cpd_settings', 'updated' => 'true'), network_admin_url('settings.php')));
			exit;  
		}

		function render_participants_page() {
			if("POST" != $_SERVER['REQUEST_METHOD']) {
				// create a table of participants

				$ParticipantTable = new Participant_Table();
				$ParticipantTable->prepare_items();
				$ParticipantTable->display();

				$this->render_add_participant_form();
			}

		}


		function add_network_dashboard_widgets() {
			global $wp_meta_boxes;
			wp_add_dashboard_widget('cpd_admin_dashboard_widget', 'CPD Unassigned Users', array($this,'unassigned_users_widget'));
			wp_add_dashboard_widget('latest_posts_histogram_widget', 'Posts by week', array($this,'latest_posts_histogram_widget'), array($this,'latest_posts_histogram_widget_config'));
			wp_add_dashboard_widget('posts_by_participants_barchart_widget', 'Posts by user', array($this,'posts_by_participants_barchart_widget'), array($this,'posts_by_participants_barchart_widget_config'));

		}
		function add_dashboard_widgets() {
			global $wp_meta_boxes;
			wp_add_dashboard_widget('latest_posts_histogram_widget', 'Weeks since post', array($this,'latest_posts_histogram_widget'), array($this,'latest_posts_histogram_widget_config'));
			wp_add_dashboard_widget('posts_by_participants_barchart_widget', 'Posts by user', array($this,'posts_by_participants_barchart_widget'), array($this,'posts_by_participants_barchart_widget_config'));
		}

		function unassigned_users_widget(){
			$orphans=$this->get_orphaned_participants();
			if(count($orphans)) { ?>
				<p>The following participants have no supervisor assigned to them:</p>
				<table>
					<tr><th>Name</th><th>Journal</th></tr> 
					<?php foreach($orphans as $p) {
						$p_journal=$this->get_cpd_journal($p['ID']);
						$user_edit_url=add_query_arg( array('user_id' => $p['ID']), network_admin_url('user-edit.php#cpd_profile' ));
						$current_scheme=is_ssl() ? 'https://' : 'http://';
						$site_url=$current_scheme.$p_journal['domain'].$p_journal['path'];
						$site_admin_url=$site_url.'wp-admin'; ?>
						<tr>
							<td><a href="<?php echo $user_edit_url ?>"><?php echo $p['user_nicename']?></a></td>
							<td><a href="<?php echo $site_url?>"><?php echo $site_url ?></a> | <a href="<?php echo $site_admin_url?>">dashboard</a></td>
						</tr>
					<?php } ?>
				</table>
				<?php 
			} else {
				echo "<p>All participants have supervisors assigned to them, and eveything is right with the world again.</p>";
			}
			$redundants=$this->get_redundant_supervisors();
			if(count($redundants)) { ?>
				<p>The following supervisors have no participants assigned to them:</p>
				<table>
					<tr><th>Name</th><th></th>
					<?php foreach($redundants as $s) {
						$user_edit_url=add_query_arg( array('user_id' => $s['ID']), network_admin_url('user-edit.php#cpd_profile' )); ?>
						<tr><td><a href="<?php echo $user_edit_url ?>"><?php echo $s['user_nicename']?></a></td></tr>
					<?php } ?>
				</table>
				<?php
			} else {
				echo "<p>All supervisors have pariticpants assigned to them. You can sleep safely now.</p>";
			}
		}

		function unassigned_users_email() {
			if($orphans=$this->get_orphaned_participants() || $redundants=$this->get_redundant_supervisors()) {
				ob_start();
				$this->unassigned_users_widget();
				$report=ob_get_clean();
				$admin_email=get_option('admin_email');
				add_filter( 'wp_mail_content_type',array($this,'set_html_content_type') );
				wp_mail($admin_email, 'CPD Unassigned Users Report', $report );
				remove_filter( 'wp_mail_content_type', array($this,'set_html_content_type'));
			}
		}

		function set_html_content_type() {
			return 'text/html';
		}

		function set_role_metadata($id, $role){
			// update cpd_role to match role
			if('supervisor'===$role || 'participant'===$role){
				update_user_meta($id, 'cpd_role', $role);
			}
		}

		function editable_roles($all_roles){
			// disallow anyone but a site admin from adding/removing any roles that can edit posts or manage users
			//
			$cpd_role=get_user_meta(get_current_user_id(), 'cpd_role', true);
			if($cpd_role=='participant') {
				$barred_caps=array('edit_users','create_users','edit_posts'); // these are the capanbilities that we don't let participants create users with
				foreach($all_roles as $rolename => $role) {
					$capabilities=$role['capabilities'];
					foreach($barred_caps as $cap){
						if(array_key_exists($cap,$capabilities) && isset($capabilities[$cap])) {
							unset($all_roles[$rolename]);
						}
					}
				}
			}
			
			return $all_roles;
		}	

		function user_has_cap($allcaps, $caps, $args) {
			$cap=$args[0];
			$user_id=$args[1];
			$removing_user_id=$args[2];
			if('remove_user'===$cap){

				if('participant'==get_user_meta($user_id,'cpd_role',true) && 'supervisor'==get_user_meta($removing_user_id,'cpd_role',true) ) {
					$allcaps['remove_users']=false;
				}
			}
			return $allcaps;
		}	

		/* supervisor access functions 
		*/
		function get_supervisors($p_id){
			// returns a  list of the supervisor for a given participant
			global $wpdb;
			return $wpdb->get_results($wpdb->prepare("SELECT ID, display_name  FROM {$this->relationship_table} 
				INNER JOIN {$wpdb->users} ON {$this->relationship_table}.supervisor_id={$wpdb->users}.ID
				WHERE {$this->relationship_table}.participant_id=%d",$p_id), ARRAY_A);
		}

		function get_all_supervisors() {
			// returns a list of all avialable supervisors
			global $wpdb;
			return $wpdb->get_results("SELECT ID, display_name
				FROM {$wpdb->users} 
				INNER JOIN {$wpdb->usermeta} ON {$wpdb->users}.ID={$wpdb->usermeta}.user_id 
				WHERE {$wpdb->usermeta}.meta_key LIKE 'cpd_role' AND {$wpdb->usermeta}.meta_value LIKE 'supervisor'", ARRAY_A);

		}

		/* participant access fucntions
		*/
		function get_participants($s_id){
			// returns a list of participant IDs and names for a given supervisor
			global $wpdb;
			return $wpdb->get_results($wpdb->prepare("SELECT ID, display_name FROM {$this->relationship_table} 
				INNER JOIN {$wpdb->users} ON {$this->relationship_table}.participant_id={$wpdb->users}.ID
				WHERE {$this->relationship_table}.supervisor_id=%d",$s_id), ARRAY_A);
		}

		function get_all_participants() {
			// returns a list off all available participants
			global $wpdb;
			return $wpdb->get_results("SELECT ID, display_name 
				FROM {$wpdb->users} INNER JOIN {$wpdb->usermeta} ON {$wpdb->users}.ID={$wpdb->usermeta}.user_id 
				WHERE {$wpdb->usermeta}.meta_key LIKE 'cpd_role' AND {$wpdb->usermeta}.meta_value LIKE 'participant'", ARRAY_A);

		}

		/* relationship access functions
		*/

		function add_supervisor_participant($s_id, $p_id) {
			// adds a supervisor/participant relationship
			global $wpdb;
			return $wpdb->query($wpdb->prepare("INSERT IGNORE INTO {$this->relationship_table} (participant_id, supervisor_id) VALUES (%d,%d)", $p_id, $s_id));
		}
		function remove_supervisor_participant($s_id, $p_id) {
			// removes a supervisor/participant relationship
			global $wpdb;
			return $wpdb->query($wpdb->prepare("DELETE FROM {$this->relationship_table} WHERE participant_id=%d AND supervisor_id=%d", $p_id, $s_id));
		}
		function remove_all_supervisor_participants($s_id) {
			// given a supervisor id, remove all their relationships with participants
			global $wpdb;
			return $wpdb->query($wpdb->prepare("DELETE FROM {$this->relationship_table} WHERE participant_id=%d"), $s_id);
		}
		function remove_all_participant_supervisors($p_id) {
			// given a participant id, remove all their relationships with supervisors
			global $wpdb;
			return $wpdb->query($wpdb->prepare("DELETE FROM {$this->relationship_table} WHERE supervisor_id=%d"), $p_id);
		}

		/* journal access functions
		*/
		function get_cpd_journal($user_id) {
			// return the primary blog of a given participant
			global $wpdb;
			return $wpdb->get_row($wpdb->prepare("SELECT {$wpdb->blogs}.* 
				FROM {$wpdb->blogs}
				INNER JOIN {$wpdb->usermeta} ON {$wpdb->blogs}.blog_id={$wpdb->usermeta}.meta_value
				WHERE {$wpdb->usermeta}.meta_key='primary_blog'
				AND {$wpdb->usermeta}.user_id=%d", $user_id), ARRAY_A); 
		}

		function get_all_cpd_journals() {
			// return all of the available blogs
			global $wpdb;
			return $wpdb->get_results("SELECT {$wpdb->blogs}.* FROM {$wpdb->blogs} WHERE NOT deleted", ARRAY_A); 
		}
		function get_participant_journals($s_id){
			// returns a list of the blogs that a supervisor should be assigned to for their participants
			global $wpdb;

			return $wpdb->get_results($wpdb->prepare("SELECT DISTINCT {$wpdb->blogs}.* FROM {$wpdb->blogs}
				INNER JOIN {$wpdb->usermeta} ON {$wpdb->usermeta}.meta_value={$wpdb->blogs}.blog_id AND {$wpdb->usermeta}.meta_key='primary_blog'
				INNER JOIN {$this->relationship_table} ON {$this->relationship_table}.participant_id={$wpdb->usermeta}.user_id 
				WHERE {$this->relationship_table}.supervisor_id=%d",$s_id),ARRAY_A);
		}

		function get_supervisor_journals($s_id) {
			// returns a list of the blogs that a supervisor is currently assigned to
			global $wpdb;

			return $wpdb->get_results($wpdb->prepare("SELECT DISTINCT * FROM {$wpdb->blogs} 
				INNER JOIN {$wpdb->usermeta} ON SUBSTRING_INDEX(SUBSTRING_INDEX({$wpdb->usermeta}.meta_key, '_', 1),'_',1)={$wpdb->blogs}.blog_id
				WHERE {$wpdb->usermeta}.meta_key LIKE 'wp_%%_capabilities' AND {$wpdb->usermeta}.meta_value LIKE '%%supervisor%%'
				AND {$wpdb->usermeta}.user_id=%d", $s_id), ARRAY_A);
		}

		function get_orphaned_participants() {
			global $wpdb;

			return $wpdb->get_results("SELECT * FROM {$wpdb->users}
				INNER JOIN {$wpdb->usermeta} ON {$wpdb->users}.ID={$wpdb->usermeta}.user_id AND {$wpdb->usermeta}.meta_key='cpd_role' AND {$wpdb->usermeta}.meta_value='participant'
				LEFT JOIN {$this->relationship_table} ON {$this->relationship_table}.participant_id={$wpdb->users}.ID
				WHERE {$this->relationship_table}.supervisor_id IS NULL", ARRAY_A);
		}

		function get_redundant_supervisors() {
			global $wpdb;

			return $wpdb->get_results("SELECT * FROM {$wpdb->users}
				INNER JOIN {$wpdb->usermeta} ON {$wpdb->users}.ID={$wpdb->usermeta}.user_id AND {$wpdb->usermeta}.meta_key='cpd_role' AND {$wpdb->usermeta}.meta_value='supervisor'
				LEFT JOIN {$this->relationship_table} ON {$this->relationship_table}.supervisor_id={$wpdb->users}.ID
				WHERE {$this->relationship_table}.participant_id IS NULL", ARRAY_A);
		}


		function update_cpd_posts($post_id) {
			global $wpdb, $site_id;

			$p=get_post($post_id);
			$sql=$wpdb->prepare("DELETE FROM {$this->posts_table} WHERE post_id=%d AND blog_id=%d AND site_id=%d", $p->ID, get_current_blog_id(), $site_id);
			$wpdb->query($sql);

			if($p->post_status=="publish"){
				$sql=$wpdb->prepare("INSERT INTO {$this->posts_table} (user_id, post_id, blog_id, site_id, post_date, post_status, post_title, guid)
					VALUES(%d, %d, %d, %d, %s, %s, %s, %s)",
					$p->post_author,
					$p->ID,
					get_current_blog_id(),
					$site_id,
					$p->post_date,
					$p->post_status,
					$p->post_title,
					$p->guid
					);
				$wpdb->query($sql);
			}
		} 

		// dashboard widgets
		function latest_posts_histogram_widget() {
			global $wpdb;

			if(get_user_meta(get_current_user_id(),'cpd_role')=='supervisor') {
				$supervisor_join=$wpdb->prepare("INNER JOIN {$this->relationship_table} ON {$this->relationship_table}.participant_id={$this->posts_table}.user_id WHERE {$this->relationship_table}.supervisor_id=%d",get_current_user_id());
			}
			$sql="SELECT YEARWEEK(CURRENT_DATE(),3)-YEARWEEK({$this->posts_table}.post_date,3) AS weeks_ago, COUNT(cpd_post_id) AS c FROM {$this->posts_table}
				$supervisor_join
				GROUP BY weeks_ago 
				ORDER BY weeks_ago ASC";

			$histogram=$wpdb->get_results($sql, OBJECT_K);

			function _getCount($a) {
				if(is_object($a) && property_exists($a,'c')) {
					return $a->c;
				} else {
					return 0;
				}
			}

			function weeks_ago_label($i){
				switch ($i) {
					case 0:
						return "this&nbsp;week";
						break;
					case 1:
						return "last&nbsp;week";
						break;
					default:
						return $i."&nbsp;weeks&nbsp;ago";
						break;
				}
			}	

			echo "<div class='latest_posts_histogram'>";
			echo "<table>";
			$counts = array_map('_getCount', $histogram);
			$weeks=intval(get_option('latest_posts_histogram_widget_weeks'));
			if(!$weeks) $weeks=4; //default to 4 weeks
			if(count($counts) && max($counts)>0) {
				for($i=0; $i<$weeks; $i++) { ?>
					<tr>
						<td><?php echo weeks_ago_label($i);?></td>
						<td width="100%">
							<div id='weeks_ago_<?php echo $i ?>' class='latest_posts_histogram_bar' style='width:<?php echo intval(100*_getCount($histogram[$i])/max($counts))?>%'><?php echo _getCount($histogram[$i])?></div>
						</td>
					</tr> <?php
				}
			}
			echo "</table></div>";
		}

		function latest_posts_histogram_widget_config() {
			function selected_if_eq($a,$b) {
				if($a===$b) {
					return " selected='selected'";
				}
			}
			$weeks=intval(get_option('latest_posts_histogram_widget_weeks'));
			if(!$_POST['latest_posts_histogram_widget_config']) {
				echo '<input type="hidden" name="latest_posts_histogram_widget_config" value="1">';
				echo 'Show the last <select name="weeks">';
				echo '	<option'.selected_if_eq($weeks,4).'>4</option>';
				echo '	<option'.selected_if_eq($weeks,8).'>8</option>';
				echo '	<option'.selected_if_eq($weeks,12).'>12</option>';
				echo '	<option'.selected_if_eq($weeks,24).'>24</option>';
				echo '</select> weeks';
			} else {
				update_option('latest_posts_histogram_widget_weeks',$_POST['weeks']);
			}
		}

		function posts_in_week() {
			global $wpdb;
			if(get_user_meta(get_current_user_id(),'cpd_role')=='supervisor') {
				$supervisor_join=$wpdb->prepare("INNER JOIN {$this->relationship_table} ON {$this->relationship_table}.participant_id={$wpdb->users}.ID AND {$this->relationship_table}.supervisor_id=%d",get_current_user_id());
			}
			if($_POST['weeks_ago']) {
				$sql=$wpdb->prepare("SELECT {$this->posts_table}.*, {$wpdb->users}.* FROM {$this->posts_table}
					INNER JOIN {$wpdb->users} ON {$wpdb->users}.ID={$this->posts_table}.user_id
					WHERE YEARWEEK(CURRENT_DATE(),3)-YEARWEEK({$this->posts_table}.post_date,3) = %d", $_POST['weeks_ago']);
				$users_posts=$wpdb->get_results($sql, ARRAY_A);
				echo "<ul class='posted_in_week'>";
				foreach ($users_posts as $post) {
					echo "<li>";
					echo "<a href='http://".add_query_arg(array('user_id' => $post['ID]']), network_admin_url('user-edit.php' )) ."'>".$post['user_nicename']."</a> posted ";
					echo "<a href='".$post['guid']."'>".$post['post_title']."</a> on ";
					echo date("d M y", strtotime($post['post_date']));
					echo "</li>";
				}
				echo "</ul>";
			}
			die();
		}


		/* dashboard widget to show a barchart of how many psots each user has posted. Only shows the participants for a supoervisor. */
		function posts_by_participants_barchart_widget($supervisor_id=null) {
			global $wpdb;

			if(get_user_meta(get_current_user_id(),'cpd_role')=='supervisor') {
				$supervisor_join=$wpdb->prepare("INNER JOIN {$this->relationship_table} ON {$this->relationship_table}.participant_id={$wpdb->users}.ID AND {$this->relationship_table}.supervisor_id=%d",get_current_user_id());
			}
			$count=intval(get_option('posts_by_participants_barchart_widget_count'));
			if($count>0) {
				$limit_sql=$wpdb->prepare("LIMIT %d", $count);
			}
			$order=get_option('posts_by_participants_barchart_widget_order')=='asc' ? 'ASC' : 'DESC';
			$sql="SELECT {$wpdb->users}.user_nicename, COUNT({$this->posts_table}.post_id) AS c
				FROM {$wpdb->users}
				LEFT JOIN {$this->posts_table} ON {$this->posts_table}.user_id={$wpdb->users}.ID
				$supervisor_join
				GROUP BY user_nicename
				ORDER BY c $order, user_nicename ASC
				$limit_sql";
			$user_posts=$wpdb->get_results($sql, ARRAY_A);
			echo "<div class='user_posts_barchart'>";
			echo "<table>";

			function _get_posts_count($a) {
				return $a['c'];
			}

			if(count($user_posts)) {
				$max_count=max(array_map('_get_posts_count', $user_posts));
				foreach($user_posts as $user) { ?>
					<tr>
						<td><?php echo $user['user_nicename'] ?></td>
						<td width="100%"><div id="posts_by_<?php echo $user['user_nicename'] ?>" class='user_posts_barchart_bar' style='width:<?php echo intval(100*$user['c']/$max_count) ?>%'><?php echo $user['c'] ?></div></td>
					</tr><?php
				}
			}
			echo "</table></div>";
		}

		function posts_by_participants_barchart_widget_config() {
			function selected_if_eq($a,$b) {
				if($a===$b) {
					return " selected='selected'";
				}
			}
			function checked_if_eq($a,$b) {
				if($a===$b) {
					return " checked='checked'";
				}
			}
			$count=intval(get_option('posts_by_participants_barchart_widget_count'));
			$order=get_option('posts_by_participants_barchart_widget_order')=='asc' ? 'asc' : 'desc';

			if(!$_POST['posts_by_participants_barchart_widget_config']) {
				echo '<input type="hidden" name="posts_by_participants_barchart_widget_config" value="1">';
				echo 'Show the <select name="count">';
				echo '	<option'.selected_if_eq($count,10).'>10</option>';
				echo '	<option'.selected_if_eq($count,20).'>20</option>';
				echo '	<option'.selected_if_eq($count,30).'>30</option>';
				echo '	<option'.selected_if_eq($count,0).' value="0">all</option>';
				echo '</select>';
				echo '<select name="order">';
				echo '<option'.selected_if_eq($order, 'desc').' value="desc">most</option>';
				echo '<option'.selected_if_eq($order, 'asc').' value="asc">least</option>';
				echo '</select>';
				echo ' prolific participants';
			} else {
				update_option('posts_by_participants_barchart_widget_count',$_POST['count']);
				update_option('posts_by_participants_barchart_widget_order',$_POST['order']=='asc' ? 'asc' : 'desc');
			}
		}

		function posts_by_user() {
			global $wpdb;
			if(get_user_meta(get_current_user_id(),'cpd_role')=='supervisor') {
				$supervisor_join=$wpdb->prepare("INNER JOIN {$this->relationship_table} ON {$this->relationship_table}.participant_id={$wpdb->users}.ID AND {$this->relationship_table}.supervisor_id=%d",get_current_user_id());
			}
			if($_POST['user_nicename']) {
				$sql=$wpdb->prepare("SELECT {$wpdb->users}.user_nicename, {$this->posts_table}.*
					FROM {$wpdb->users}
					INNER JOIN {$this->posts_table} ON {$this->posts_table}.user_id={$wpdb->users}.ID
					$supervisor_join
					WHERE {$wpdb->users}.user_nicename=%s", $_POST['user_nicename']);

				$users_blogs=$wpdb->get_results($sql, ARRAY_A);
				echo "<ul class='posts_by_user'>";
				foreach ($users_blogs as $users_post) {
					echo "<li><a href='".$users_post['guid']."'>".$users_post['post_title']."</a> on ".date('d M y', strtotime($users_post['post_date']))."</li>";
				}
				echo "</ul>";
			}
			die();
		}


		/* profile form rendering and processing
		*/
		function edit_cpd_profile($user) {
			if(!is_super_admin()) {
				return;
			}

			$user_supervisors=$this->get_supervisors($user->ID);
			$all_supervisors=$this->get_all_supervisors();
			$user_participants=$this->get_participants($user->ID);
			$all_participants=$this->get_all_participants();
			$cpd_journal=$this->get_cpd_journal($user->ID);
			$all_cpd_journals=$this->get_all_cpd_journals();

			function checked_if_in_list($o, $list) {
				if(!count($list)) return;
				foreach($list as $l) {
					if($l==$o) {
						echo "checked=\"checked\"";
						return;
					}
				}
			}
			function selected_if_eq($a,$b){
				if($a===$b) {
					echo "selected=\"selected\"";
				}
			}
			function supervisor_checkbox($sv, $user_supervisors) {
				echo "<span><input type=\"checkbox\" name=\"cpd_supervisors[]\"";
				echo " value=\"{$sv['ID']}\"";
				echo " id=\"cpd_supervisor_{$sv['ID']}\"";
				echo checked_if_in_list($sv, $user_supervisors)." />";
				echo "<label for=\"cpd_supervisor_{$sv['ID']}\">".htmlentities($sv['display_name'])." </label></span>";
			}
			function participant_checkbox($pt, $user_participants) {
				echo "<span><input type=\"checkbox\" name=\"cpd_participants[]\"";
				echo " value=\"{$pt['ID']}\"";
				echo " id=\"cpd_participant_{$pt['ID']}\"";
				echo checked_if_in_list($pt, $user_participants)." />";
				echo "<label for=\"cpd_participant_{$pt['ID']}\">".htmlentities($pt['display_name'])." </label></span>";
			}
			function journal_option($j, $cpd_journal) {
				echo "<option value=\"{$j['blog_id']}\"";
				echo selected_if_eq($j['blog_id'], $cpd_journal['blog_id']);
				echo ">http://{$j['domain']}{$j['path']}</option>";
			}

			?>
			<a name="cpd_profile"></a>
			<div id="cpd_profile">
				<h3>CPD Profile</h3>
				<table class="form-table">
					<tbody>
						<tr>
							<th>CPD Role</th>
							<td> <?php
								$cpd_role=get_user_meta($user->ID, 'cpd_role', true);
								?>
								<select id="cpd_role" name="cpd_role">
									<option value="">No Role</option>
									<option value="participant" <?php selected_if_eq($cpd_role, "participant") ?>>Participant</option>
									<option value="supervisor" <?php selected_if_eq($cpd_role, "supervisor") ?>>Supervisor</option>
								</select>
							</td>
						</tr>
						<tr class="cpd_journals">
							<th>CPD Journal</th> 
							<td>
								<select id="cpd_journal" name="cpd_journal">
									<option value="new">Create a new journal</option>
									<?php
									if(count($all_cpd_journals)) {
										foreach($all_cpd_journals as $j) {
											journal_option($j, $cpd_journal);
										}
									}
									?>
								</select>
							</td>
						</tr>
						<tr class="cpd_supervisors">
							<th>Allocated supervisors</th> 
							<td>
								<?php
								if(count($all_supervisors)) {
									foreach($all_supervisors as $sv) { 
										supervisor_checkbox($sv, $user_supervisors); 
									}
								} ?>
							</td>
						</tr>
						<tr class="cpd_participants">
							<th>Allocated participants</th>
							<td>
								<?php
								if(count($all_participants)) {
									foreach($all_participants as $pt) { 
										participant_checkbox($pt, $user_participants); 
									}
								} ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php

		}

		function update_cpd_profile($user_id) {
			global $errors, $wpdb;

			function extract_array_key($list_of_arrays, $key) {
				$e=array();
				foreach($list_of_arrays as $a){
					array_push($e, $a[$key]);
				}
				return $e;
			}

			if(!is_super_admin()) { // sorry hombre, only network admins get to mess with people's cpd_role
				return;
			}
			if(!isset($_POST['cpd_role'])) { // this just isn't going to work out
				return;
			}
			if ( ! is_object($errors) ) {
				$errors = new WP_Error();
			}
			$user=get_userdata($user_id);
			$user_data=$user->data;
			$current_cpd_role=get_user_meta($user_id,'cpd_role',true);

			if("participant"==$_POST['cpd_role']) {
				if('supervisor'==$current_cpd_role) {
					$current_journals=$this->get_supervisor_journals($user_id);
					if(count($current_journals)>0) {
						foreach($current_jounrals as $j) {
							remove_user_from_blog($user_id, $j['blog_id']);
						}
					}
					$this->remove_all_supervisor_participants($user_id);
				}

				$cpd_journal=$_POST['cpd_journal'];
				if("new"==$cpd_journal) {
					// wpmu_create_blog -- or summit
					$current_site=network_site_url();
					$domain=parse_url(network_site_url(), PHP_URL_HOST);
					$path=parse_url(network_site_url(), PHP_URL_PATH).$user_data->user_login."/";

					$cpd_settings=get_option('cpd_new_blog_options');
					$cpd_settings=preg_replace('/[\n\r]+/', '&', $cpd_settings);
					$cpd_settings=preg_replace('/[\s\:]+/', '=', $cpd_settings);
					parse_str($cpd_settings, $options);

					$cpd_journal=wpmu_create_blog($domain, $path, "CPD Journal for ".$user_data->user_nicename, $user_id, $options, 1);
					if(!$cpd_journal) {
						$errors->add_error("journal_creation_failed",__("Failed create a journal for ").$user_username);
						return;
					}
				}
				if($cpd_journal!=$this->get_cpd_journal($user_id)) { //if the journal picked is not the current one for this user
					// add user to blog
					$r=add_user_to_blog($cpd_journal, $user_id, 'participant');
					if(is_wp_error($r)) {
						$errors=$r;
						return;
					}
					update_user_meta($user_id, 'primary_blog', $cpd_journal);
				}
				$all_supervisors=extract_array_key($this->get_all_supervisors(),'ID');
				$post_supervisors=$_POST['cpd_supervisors'];
				$post_supervisors= is_array($post_supervisors) ? $post_supervisors : array();
				$current_supervisors=extract_array_key($this->get_supervisors($user_id),'ID');

				if(count($all_supervisors)>0) {
					foreach($all_supervisors as $s ) {
						if(in_array($s, $post_supervisors) && !in_array($s, $current_supervisors)) {
							$this->add_supervisor_participant($s,$user_id);
						} else if(!in_array($s, $post_supervisors) && in_array($s, $current_supervisors)) {
							$this->remove_supervisor_participant($s, $user_id);						
						}
					}
				}
			} else if("supervisor"==$_POST['cpd_role']) {

				// iterate the participants adding and removing the participants
				$all_participants=extract_array_key($this->get_all_participants(),'ID');
				$post_participants=$_POST['cpd_participants'];
				$post_participants= is_array($post_participants) ? $post_participants : array();
				$current_participants=extract_array_key($this->get_participants($user_id), 'ID');
				if(count($all_participants)>0) {
					foreach($all_participants as $p) {
						if(in_array($p, $post_participants) && !in_array($p, $current_participants)){
							// add a new participant relationship
							$this->add_supervisor_participant($user_id, $p);
						} else if(!in_array($p, $post_participants) && in_array($p, $current_participants)) {
							$this->remove_supervisor_participant($user_id, $p);
						}
					}
				}
				// make sure the supervisor is on each of the pariticpants' primary blog
				$all_cpd_journals=extract_array_key($this->get_all_cpd_journals(), 'blog_id');
				$should_have_journals=extract_array_key($this->get_participant_journals($user_id), 'blog_id');
				if(count($all_cpd_journals)>0){
					foreach($all_cpd_journals as $j){
						if(in_array($j, $should_have_journals)) {
							add_user_to_blog($j, $user_id, 'supervisor');
						} else {
							remove_user_from_blog($user_id, $j);
						}
					}
				}
			} else {
				$_POST['cpd_role']="none";
				if('supervisor'==$current_cpd_role) {
					$current_journals=$this->get_supervisor_journals($user_id);
					if(count($current_journals)>0){
						foreach($current_journals as $j) {
							remove_user_from_blog($user_id, $j['blog_id']);
						}
					}
					$this->remove_all_supervisor_participants($user_id);
				}
				if('participant'==$current_cpd_role) {
					$this->remove_all_participants_supervisors($user_id);
				}
			}
			update_user_meta($user_id,'cpd_role', $_POST['cpd_role']);
		}

		/* user admin table stuff */ 
		function add_cpd_role_column($columns) {
			$columns['cpd_role'] = 'CPD Role';
			return $columns;
		}

		function cpd_role_column($value, $column_name, $id) {
			if($column_name=='cpd_role') {
				return get_user_meta($id,'cpd_role', true);
			}
		}

		function add_cpd_role_column_sort($columns)
		{
			$columns['cpd_role'] = 'cpd_role'; 
			return $columns; 
		}

		function filter_and_order_by_cpd_column($query)
		{

			global $wpdb; 
			$vars = $query->query_vars;
			if(isset($_GET['cpd_role']) ){
				$query->query_from .= " LEFT JOIN ".$wpdb->prefix."usermeta n ON (".$wpdb->prefix."users.ID = n.user_id  AND n.meta_key = 'cpd_role')"; 				
				$query->query_where.=$wpdb->prepare(" AND n.meta_value =%s", $_GET['cpd_role']);
			} elseif($vars['orderby'] === 'cpd_role') {
				$query->query_from .= " LEFT JOIN ".$wpdb->prefix."usermeta m ON (".$wpdb->prefix."users.ID = m.user_id  AND m.meta_key = '{$vars[orderby]}')"; 
				$query->query_orderby = "ORDER BY m.meta_value ".$vars['order'];
			} 

		}

		function add_cpd_role_views($views) {
			$num_supervisors=count($this->get_all_supervisors());
			$num_participants=count($this->get_all_participants());
			$views['supervisors'] = "<a href='" . network_admin_url('users.php?cpd_role=supervisor') . "'$class>" . sprintf( _n( 'Supervisors <span class="count">(%s)</span>', 'Supervisors <span class="count">(%s)</span>', $num_supervisors ), number_format_i18n( $num_supervisors ) ) . '</a>';
			$views['pariticpants'] = "<a href='" . network_admin_url('users.php?cpd_role=participant') . "'$class>" . sprintf( _n( 'Participants <span class="count">(%s)</span>', 'Participants <span class="count">(%s)</span>', $num_participants ), number_format_i18n( $num_participants ) ) . '</a>';
			return $views;
		}

		// Send mail when a post is published or updated
		function cpdje_send_mail_on_update( $post_id ) {

			//global $cpd;

			// If this is just a revision, don't send the email.
			if ( wp_is_post_revision( $post_id ) )
				return;

			// If the CPD Journal plugin isnt running, return
			//if( !is_object( $cpd ) )
			//	return;

			$saved_post 	= get_post( $post_id );

			// Only email if the post is published
			if( $saved_post->post_status == 'publish' )
			{
				$post_title 	= $saved_post->post_title;
				$post_url 		= get_permalink( $post_id );
				$post_author_id	= $saved_post->post_author; 
				$post_author 	= get_userdata( $post_author_id );
				$subject 		= $post_author->display_name . ' has updated their journal \'' . wp_title( '', false ) . '\'';
				$message 		= '';

				//Create the message
				$message		.= '<p>The participant <strong>' . $post_author->display_name . '</strong> had updated their journal \'<strong>' . wp_title( '', false ) . '</strong>\' with the entry: <strong><a href="'. $post_url .'">'. $post_title .'</a></strong></p>';
				$message		.= '<p>Options:</p>';
				$message		.= '<ul>';
				$message		.= '<li><a href="'. $post_url .'">View the journal entry: <strong>'. $post_title .'</strong></a></li>';
				$message		.= '<li><a href="'. $post_url .'#reply-title">Leave a comment on: <strong>'. $post_title .'</strong></a></li>';
				$message		.= '</ul>';

				// Get the supervisors of the author
				$supervisors = $this->get_supervisors( $post_author_id );

				// Email each supervisor
				foreach ( $supervisors as $supervisor )
				{
					$supervisor = get_userdata( $supervisor['ID'] );

					$message = '<p>Dear <strong>'. $supervisor->display_name .'</strong>,</p>' . $message;

					wp_mail( $supervisor->user_email, $subject, $message );
				}
			}
		}

		// Set the mail types to HTML
		function cpdje_set_content_type( $content_type )
		{
			return 'text/html';
		}

		// If the user is a participant, limit the options they see in the menu
		function cpdpp_remove_admin_menus() 
		{
			$user = wp_get_current_user();

			$user_type = get_user_meta( $user->ID, 'cpd_role', true );

			if( $user_type == 'participant' )
			{
				remove_submenu_page( 'tools.php', 'ms-delete-site.php' );
			}
		}
	}
}

ob_start();
$cpd = new cpd_journal();