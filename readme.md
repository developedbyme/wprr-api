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

### 0.10.4
* Ordering for ranges

### 0.10.3
* More performance on getting images

### 0.10.2
* Correct order for children

### 0.10.1
* Correct value for pagination

### 0.10.0
* Added object for data beloning to the query

### 0.9.11
* Added language description
* Added meta to terms

### 0.9.10
* Added encoding for oembed

### 0.9.9
* Fixed images
* Check if file exists

### 0.9.8
* Encoding media files in acf

### 0.9.7
* Added slug to encoded terms

### 0.9.6
* Added possibility to select multiple terms for ranges

### 0.9.5
* Added encoding of acf taxonomies

### 0.9.4
* Added image range endpoint

### 0.9.3
* Added post range endpoint

### 0.9.2
* External availability to encode request

### 0.9.1
* Encoding acf galleries

### 0.9.0
* Added output buffer for php messages

### 0.8.5
* Removed notice when image sizes are missing
* Added endpoint for customizer data

### 0.8.4
* Added more data to images

### 0.8.3
* Encoding acf repeater fields
* Moved encoding of images

### 0.8.2
* Added encoding of acf fields

### 0.8.1
* Added children to posts
* Fixed misspelled domains

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