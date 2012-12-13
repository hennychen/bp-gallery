=== BuddyPress BP Gallery Plus ===
Contributors: caevan
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=YSM3KMT3B5AQE 
Tags: BuddyPress, BP, album, albums, picture, friend tagging, face tags, friend tags, photo tags, photo tagging, face tagging, bp-media, pictures, photos, gallery, galleries, media, privacy, buddypress, social networking, activity, profiles, messaging, friends, groups, forums, microblogging, social, community, networks, networking, cms
Requires at least: 3.1.0
Tested up to: 3.4.1
Stable tag: 1.1
License: GPLv2 or later

BP users can create multiple Galleries for BuddyPress. Includes Posts to Wire, Member Comments, and Gallery Privacy Controls with group control.


== Description ==
This plugin adds full photo gallery capabilities to BuddyPress, enabling users to create multiple galleries and control the visibility of each album, with the added feature of Face-Tagging and Like.

This is an early release version of the plugin. You should test it extensively before using it on a production site. It provided on an “as is” basis without any warranty. We are not liable for any damage or losses.


== Installation ==

1. Download bpGallery from http://www.amkd.com.au/wp-content/uploads/BPGallery.zip and unzip on your local disk
2. Upload `bpGallery` folder to the `/wp-content/plugins/` directory or use automatic installation from wp plugin panel
3. Activate the plugin through the 'Plugins' menu in WordPress
4. You can use the default settings select BP Gallery Plus option in the admin screen to view and change them as required.
5. You can configure which lightbox tye image viewer you want to use. Fancybox is the default and comes with the plugin. You can use Thickbox or Colorbox if you prefer.
6. To speed up image viewing BP Gallery Plus can be configured to load the mid-sized image rather than the full size. If using Fancybox the full sized image can be viewed by clicking the "Full Size" button
7. The plugin creates a new profile group called BPGallery and a new profile field called "Donation Link". The user just needs to add a valid Paypal donation link for a donation icon to appear at the top of each gallery
8. If the donation link filed does not appear after the upgrade, deactivate and activate the plugin. The new field will appear.


== Other Notes ==

BuddyPress BP Gallery Plus is based of the Photos+tags plugin. Localization/I18n is not fully supported yet

== Frequently Asked Questions ==

= Will BP Gallery Plus coexist with BP Photos+Tags =
No as BPGallery Plus will not coexist with BP Photos+Tags, it is a replacement for that plugin. You should uninstall BP Photos+Tags before activating BP Gallery
== Screenshots ==

1. **Upload Photos** - Upload photos screen with album selection, multiple file upload and visibility. 
2. **Gallery Content** - Gallery content management.
3. **View Galleries** - Members galleries.
4. **Add New Gallery** - Add new gallery screen.


== Changelog ==

= 1.0 =
* "First Release."

= 1.1 = 
* "Added gallery donations"

= 1.1.1 = 
* "Added missing donation icon file."

= 1.1.2 = 
* "Fixed issue where profile group BP Gallery and donation field where added each time the plugin was activated."

= 1.1.3 = 
* "Fixed issue where install path if installed through wordpress was different from install path when loaded from download."

= 1.1.4 = 
* "Fixed incorrect path to tagging javascript"

= 1.1.5 = 
* "Changed Plugin name to BP Gallery Plus to avoid conflict with existing BP Gallery plugin."

= 1.2.0 = 
* "Function names and table names have all been renamed to avoid conflict with existing plugins thanks to Louis [EllsWeb]. Fancybox is now supplied as the default image viewer, with the added ability to view the mid sized imaged the click to load the full size image. Also can be configured to support colourbox and thickbox. Pagination issues have been addressed and fixed. Phototagging is fixed as well"

= 1.2.1 = 
* "Fixed issue edit album page did no load correctly, image edit no available on the Image Tab, Admins can now edit users albums and images,'Liked' images loads correctly"
= 1.2.2 = 
* "Fixed error when loading new albums. Feature image no longer displayed in activity stream for galleries other than those marked public and registered members"

== Upgrade Notice ==
= 1.0 =
First release
= 1.1 =
With this release users can now have a donation link and button appear on the galleries, by entering a valid Paypal doantion link into their profile.
= 1.1.1 =
Added missing donation icon file
= 1.1.2 = 
Upgrade to this version otherwise BP Gallery profile group will be created each time you activate the plugin resulting in multiple copies of the same group.
= 1.1.3 = 
If you have installed a previous version through wordpress the plugin will not function correctly. this upgrade will change your install directory.
= 1.1.4=
Fixed incorrect path to tagging javascript
= 1.1.5=
Change plugin name to BP Gallery Plus to avoid conflict with the existing BP Gallery Plugin
= 1.2.0=
Fixed capatability issues, Now comes with Fancybox for image viewing, can support Thickbox and Colorbox if available. Pagination works. Phototagging is fixed.
= 1.2.1=
Admins can edit users albums and images. Image edit now possible from the Image tabe under My Galleries
= 1.2.2=
* "Fixed error when loading new albums. Feature image no longer displayed in activity stream for galleries other than those marked public and registered members"
== Help Out! ==

If you want to help out, you can send some cash over [PayPal](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=YSM3KMT3B5AQE).
