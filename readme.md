# prag-file-list-shortcode
This plugin provides some simple organization of files in the Media library of WordPress as well as a shortcode for listing files within defined group(s).  Files listed by the shortcode are ordered alphabetically by file name/title.

The file listing is generated every time the page is generated (i.e. if files are added/edited/deleted from a folder, those changes will be reflected on any page using the shortcode the next time the page is generated)

## Organization
This plugin adds a "Folder" taxonomy to the media library (similar to categories for posts).  Files can be added to these folders while editing a file via the WordPress Media library.

## Managing Folders
A menu item for "Folders" is added to the WordPress Admin menu under "Media".  You can add/edit/delete folders from here.  As files are assigned to folders, you can also come here and click on the number in the "Files" column for a given folder to see a filtered list of files associated with that folder.

## Usage

### Syntax
`[file-list folder="<folder-slug>" _relation="<AND/OR>"_]`

`<folder-slug>` is the slug for the folder that you want to display.  Easy copy & paste shortcodes for single folders are provided on the **Folders** page under the **Media** menu item in the WordPress Admin.  There is support for multiple folders by using a comma separated list of the folder slugs (see examples section below).

The `relation` parameter is optional.  Valid values are `AND` or `OR`.  If multiple folders are supplied and no relation parameter is given, then it defaults to `AND`.

### Examples
- This will provide an Unordered list (`<ul>`) of all files in the folder "foo"

`[file-list folder="foo"]`

- This will provide an Unordered list (`<ul>`) of all files that are in both folders "foo" AND "bar"

`[file-list folder="foo,bar"]`

- This will provide an Unordered list (`<ul>`) of all files that are in both folders "foo" AND "bar" (i.e. the relation parameter is optional)

`[file-list folder="foo,bar" relation="AND"]`

- This will provide an Unordered list (`<ul>`) of all files that are in either folder "foo" OR "bar" (i.e. displays all files in both folders)

`[file-list folder="foo,bar" relation="OR"]`

