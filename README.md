custom-field-suite-options-pages
================================

Options pages for Custom Field Suite

### Functions for use in theme development.

    create_option_page($title, $slug);

$title is the title given to the options page.
$slug is the code you will use to reference the options page

Function checks to see if options page exists, if not, it will create it.


	delete_option_page($title, $slug);

Will delete the options page given using the title and the $slug.  Must call
this function directly to force deletion of page.  This is done to prevent
the option page from deleting in the event the `create_option_page` function is
removed from the functions.php file by mistake.