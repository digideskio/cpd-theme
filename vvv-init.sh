# Init script for VVV Auto

echo "Commencing cpd-theme Setup"

# Make a database, if we don't already have one
echo "Creating database (if it's not already there)"
mysql -u root --password=root -e "CREATE DATABASE IF NOT EXISTS cpd-theme"
mysql -u root --password=root -e "GRANT ALL PRIVILEGES ON cpd_theme.* TO wp@localhost IDENTIFIED BY 'wp';"

# Download WordPress
if [ ! -d htdocs/wp-admin ]
then
	echo "Installing WordPress using WP CLI"
	cd htdocs
	wp core download --allow-root
	wp core config --dbname="cpd_theme" --dbuser=wp --dbpass=wp --dbhost="localhost" --allow-root
	wp core install --url=cpd-theme.dev --title="CPD Theme" --admin_user=admin --admin_password=password --admin_email=hello@makedo.in --allow-root
	cd ..
fi

# The Vagrant site setup script will restart Nginx for us

echo "cpd site now installed";