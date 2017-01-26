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