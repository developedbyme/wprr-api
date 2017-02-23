# mRouter data

This plugin enables data to be outputted at json for any url in wordpress.

## Usage

Add the query ?mRouterData=json to any url to get output as json.

## Installation
### From your WordPress dashboard

1. Visit 'Plugins > Add New'
2. Search for 'mRouter data'
3. Activate mRouter data from your Plugins page.

### From WordPress.org
1. Upload the folder `m-router-data` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

## Changelog

### 0.8.0
* Added encoding of post links
* Added parent to posts

### 0.7.6
* Added end point to get data by id

### 0.7.5
* Added acf encoding
* Refactored encoding to external class

### 0.7.4
* Fixed problem with posts page

### 0.7.3
* Added gravatar hash

### 0.7.2
* Added wordpress version to meta data
* Fixed content and excerpt for queriedData

### 0.7.1
* Not loading main.css that isn't in use

### 0.7.0
* Added post thumbnails to output
* Changed all link attributes to permalink

### 0.6.2
* Fixed problems with getting authors

### 0.6.1
* Added additional checks for front page

### 0.6.0
* Added queried data to response
* Added encoding of users
* Added encoding of terms
* Added base classes for react areas

### 0.5.1
* Moved encoding of post to separate function

### 0.5.0
* Added webpack for notices and react areas in admin

### 0.4.0
* Changed structure to use odd core

### 0.3.0
* Changed format to have a main data field and a main metadata field

### 0.2.4
* Added publish date

### 0.2.3
* Changed id to lower case
* Added type and status to post
* Added post type to template selection

### 0.2.2
* Added permalink to posts
* Added terms to post

### 0.2.1
* Moved template selection parameters to own object
* Made query and queried object private to be used for debug only

### 0.2.0
* Added posts

### 0.1.1
* Added readme.
* Added query to response.

### 0.1
* First release.