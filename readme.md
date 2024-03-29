# WPRR API

This plugin enables data to be outputted at json for any url in wordpress.

## Usage

Add the query ?mRouterData=json to any url to get output as json.

## Installation
### From your WordPress dashboard

1. Visit 'Plugins > Add New'
2. Search for 'WPRR API'
3. Activate WPRR API from your Plugins page.

### From WordPress.org
1. Upload the folder `wprr-api` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

## Data structures for data API

### CurrentUserData

```
{
	id: int,
	login: string,
	email: string,
	name: string,
}
```

## Data API Endpoints

### Get the current user

Gets the details for the current user

#### Request

GET `/wp-content/plugins/wprr-api/data/me/`

#### Response

```
{
	code: "success",
	data: {
		user: CurrentUserData,
		restNonce: string
	}
}
```

#### Errors

Response for not signed in

```
{
	code: "success",
	data: null
}
```

## Changelog

### 0.17.0
* Started to move over to the wprr namespace

### 0.16.0
* New endpoint for range

### 0.15.0
* Acf encoding of meta

### 0.14.0
* First version of rendering

### 0.13.4
* Added hook for encoding of term

### 0.13.3
* Correct translation of acf options

### 0.13.2
* Image redirection

### 0.13.1
* Added support for id redirect

### 0.13.0
* Added support for add-ons

### 0.12.0
* Changed loading of acf values to support options

### 0.11.8
* Added menu end point

### 0.11.7
* Added prepare of encoding

### 0.11.6
* Added endpoint to get acf options

### 0.11.5
* Check for permissions on api endpoints

### 0.11.4
* Added attachment upload
* Added check for permissions

### 0.11.3
* External encoding of terms

### 0.11.2
* New edit post function

### 0.11.1
* Apply filters for wysiwyg in acf repeater

### 0.11.0
* Added custom ranges
* Changed domain
* Added encode_post_link as an external function

### 0.10.8
* Correct encoding of page links

### 0.10.7
* Adding image data to attachment

### 0.10.6
* Encoding flexible content

### 0.10.5
* Added performance data

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