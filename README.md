Continuous Professional Development Test
========================================

Notes
-----

1. When writing SCSS, make sure you make it nice and modular using SMACSS (https://smacss.com/).
2. The instructions below are aimed at Mac OSX users.

First time install
---------------------

1. Install VVV (https://github.com/Varying-Vagrant-Vagrants/VVV) on your local machine
2. Have Grunt, npm, Sass (3.3+) and Bower installed (see the various websites for how to do this). Composer might come in handy too (more on this later)
3. Make sure you have installed the Vagrant Hostupdater plugin (`vagrant plugin install vagrant-hostsupdater`)
4. Install the Vagrant Triggers plugin (`vagrant plugin install vagrant-triggers`)
5. cd to the 'www' folder within VVV (`cd [your-vvv-location]/www`)
6. git clone this repo into the www folder
7. copy the database from the sql folder within this repo to `[your-vvv-location]/database/backups` this will restore the DB when you run vagrant provision
8. cd to your VVV folder (`cd [your-vvv-location]`)
9. stop vagrant (`vagrant halt`)
11. provision vagrant (`vagrant up --provision`)
12. cd to the repo folder within VVV (`cd [your-vvv-location]/www/cpd`)
13. `npm install` - will install all necessary Grunt packages
14. `bower update` - install all 3rd party libraries (important for CSS generation w/ Sass)
15. `grunt modernizr` - produces a custom Modernizr build. Need only be run once at start of project, or whenever a new Modernizr test is required.
16. `grunt` - This will run in development mode, i.e. css mapping, expanded output of css.
17. visit `cpd.dev` in your browser
18. You will have to turn on the theme and the plugins in WP Admin

All this new image grunt stuff, what do I need?
-------------------------------------------------------------

1. You will need to install XQuartz from here http://xquartz.macosforge.org/trac
2. Install homebrew `ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"`
3. Install cairo `brew install cairo`
4. Install imagemagick `brew install imagemagick`
5. delete your node_modules folder and run `sudo npm install`

I'm missing dependancies
-----------------------------------
Some of the plugins, particularly the ones that start with MKDO require dependancies installing. You can do this with Git:

`git submodule update --init --recursive`

Remember
---------------

1. `npm install` - this update any Grunt plug-ins that may have been added
2. `bower update` - this will look for any new 3rd party libraries
3. `grunt modernizr` - Maybe the website is now dependant on additional Modernizr tests? Lets regenerate Modernizr just in case.
4. `grunt` - Re-running this will regenerate your JS and CSS
5. visit `cpd.dev` in your browser

HELP Im getting a message saying I cannot connect to SQL
-------------------------------------------------------------------------------

This sometimes happens when the vagrant box has been taken down. To fix simply:

1. cd to your VVV folder (`cd [your-vvv-location]`)
2. SSH to your VVV install (`vagrant ssh`)
3. Start MySQL (`sudo service mysql start`)
4. Exit SSH (`exit`)

Visit `cpd.dev` in your browser and everything should be back to normal