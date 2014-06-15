custom-field-suite-options-pages
================================

Options pages for Custom Field Suite

### Functions for use in theme development.

The base class for the Custom Field Suite Options Page is

CFS_Options

and is referenced by the variable $cfsop;

###Callable Functions

    $cfsop->add_page($title, $slug);

$title is the title given to the options page.
$slug is the code you will use to reference the options page

Function checks to see if options page exists, if not, it will create it.


	$cfsop->delete_page($title, $slug);

Will delete the options page given using the title and the $slug.  Must call
this function directly to force deletion of page.  This is done to prevent
the option page from deleting in the event the `create_option_page` function is
removed from the functions.php file by mistake.

	$cfsop->get($field, $page, $options);

$field will display the field given as it would in the regular Custom Field Suite.
$page will check the options pages for the title OR slug used.  If no page is entered, it will use the default page.
If the page is not found, it will return NULL.
$options identical to what would be entered in the regular Custom Field Suite.

