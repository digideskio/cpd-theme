=== CPD-Journals ===
Plugin URI: http://czndigital.com/wp/cpd-journals
Tags: Multisite, network, continual professional development, CPD
Requires at least: 3.4
Tested up to: 3.6
Version: 0.3
Stable tag: 0.3
Author: Saul Cozens
Author URI: http://saulcozens.co.uk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Turns a WordPress Multisite installation into a CPD (Continuous Professional Development) journal platform.

== Description ==
This is a plug-in to manage and support cohorts of people through a continuous professional development process by providing a platform for them to keep journals of their CPD activities.  By providing a journal, participants in the CPD programme are encouraged to reflect on the things they are learning and to share their eperience and practice with others.

It turns a standard WP Multisite install into a network of participants and supervisors (new user roles).  Each participant has their own journal blog that they can administer with all of the capabilities of an admin except for:
*	they cannot add, remove or edit any other administrators
*	they cannot edit or remove the posts or pages of other users

Supervisors have all the rights of an administrator except the ability to add/edit other administrators. They are also provided with additional dashboard widgets to show them which of their assigned participants have posted recent and most frequently.

The network administrator has full site-admin permissions and is responsible for adding new participants and assigning them to supervisors. The network admin is sent an email at 2am each morning informing them is any participants are not assigned a supervisors or vice versa.

== Installation == 
This plugin installs like any other WP plugin, but **MUST** be activated for the entire network 

1. Upload the plugin folder and files to /wp-content/plugins/
1. Network Activate the plugin via the Network Administrator's Plugins menu in WordPress
1. Set any default options for new journal-blogs using the Settings->CPD setting menu (eg. comment_whitelist=0 to turn off comment moderation by default)

== Screenshots ==
1. screenshot of the dashboard widgets
2. screenshot of the participant/supervisor assignment interface

== Changelog ==
= 0.1 =
Initial release

= 0.3 =
* Added funcionality to email when a journal is updated
* Added lock down to participant permissions

== Other Notes ==
= Documentation for Network Administrators =
*	Adding Participants and Supervisors
You can add Participants and Supervisors like any other users, but after adding the new user you are taken to the new user's profile where you are able to:
	*	set them as a Participant or a Supervisor
	*	create a new journal (if they are a Participant). It is possible for Participants to share a journal. 
	*	assign them a Supervisor (if they are a Participant). It is possible to assign more than one Supervisor to a Participant.
	*	assign them one or more Participants (if they are a Supervisor).

*	Editing and deleting Participants and Supervisors
	*	Deleting a Participant will **not** delete their journal, just remove their ability to edit it. If you need to delete a journal, delete both the user and the site associated with that Participant.
	*	Editing and updating a Supervisor user profile will restore that Supervisor's permissions on their Participants' journals.  This is useful if another Supervisor (or site-admin) accidentally removes a Supervisor.

*	Participant activity
There are 2 additional dashboard widgets available to allow Administrators and Supervisors to monitor Participant activity (and to identify where to provide support and encouragement).
	* Posts by Week shows a graph of how many posts have been created in the past few weeks. Each week bar can be interogated to reveal what was posted in that week.
	* Posts by user shows a graph most/least active users. Again each graph bar can be interogated to show what has been posted by that user.

*	Orphaned Participants and redundant Supervisors
Another dashboard widget shows a list of Participants who do not have Supervisors assigned to them and any Supervisors with no Paticipants to look after. This information is also emailed to the Site Administrator each night at 2am.

*	Settings
At present the only configuration option available for the CPD journals plugin is the default options for new journal/blogs. These options are passed to the [function wpmu_create_blog](http://codex.wordpress.org/WPMU_Functions/wpmu_create_blog) as the $meta paratmeter when new journals are created. This allows the site-admin to set defaults such as:
	*	stylesheet=my_journal_stylesheet (to set a default child-theme)
	*	comment_whitelist=0 (to turn off comment moderation - as all commenting is restricted to CPD users anyway)

*	Themes and plugins
The Site Administrator can install and enable any standard WordPress themes and plugins.  However, care should be taken to ensure that themes/plugins that allow abitrary PHP code to be inserted and run by Participants (or Supervisors).  Providing a wide variety of configuratable themes to allow Participants to personalise thir journals and feel more ownership over them.

*	The default blog
Every new user (Participants, Supervisors or standard WordPress users) get subscriber access to the default site blog.  This allows the default blog to be used to keep all users upto date with what is happening on the platform as a whole or news about the CPD programme itself.

= Documentation for Supervisors =
A Supervisor is essentially an administrator for a standard WordPress site. This allows them to do all the things that the Network Administrator allows them to, generally this includes:
*	adding, removing and editing users' profiles
*	creating, editing and deleting all posts and pages
*	activating and configuring plugins and themes (but not installing)
As this is all standard WordPress functionality, help, advice and support can be found on the [WordPress.org](http://wordpress.org) documentation and support community.

*	Monitoring Participant activity
Participant activity should be regularly and frequently monitored to ensure that those who are active are recognised and rewarded with feedback and those who are not engaging are encouraged with advice and support. There are 2 additional dashboard widgets available to allow Supervisors to monitor Participant activity (and to identify where to provide support and encouragement).
	*	Posts by Week shows a graph of how many posts have been created in the past few weeks. Each week bar can be interogated to reveal what was posted in that week.
	*	Posts by user shows a graph most/least active users. Again each graph bar can be interogated to show what has been posted by that user.

= Documentation for Participants =
Participants are administrators of their own journal and have all of the standard permissions that a WordPress Admin has, except:
*	They cannot edit or delete the posts or pages of other users
*	They cannot edit the profile or users who are Supervisors or Administrators. Nor can they create new users in these roles.
For help and advice on how to use WordPress to write and share CPD journals, refer to the [WordPress.org](http://wordpress.org) support community.

= Credits =
This plugin was developed by [Saul Cozens](http://saulcozens.co.uk) of [CZN Digital](http://czndigital.com), but was paid for by [Sheffield University](http://shef.ac.uk) who requested that it be written to be useful to other organisations as well and released to the community as Open Source. Thank you to them.
