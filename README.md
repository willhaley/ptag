##Primary Tag

Allows the user to designate a primary tag of the existing tags associated with a post

###Installation

Clone the plugin to your `wp-content/plugins/` directory

###Usage

> **Admin Area**
> - An additional line is added to the Publish Metabox area that will display and allow you to assign the primary tag for the post.
> - The primary tag must be selected from tags that are already associated with the post.  You cannot mark a tag that isn't associated with post as a primary tag.
> - If a primary tag is not set for the post or no tags are associated with the post, the primary tag will state "Not Set"
> - If you removed the primary tag from the tags associated with the post, the post's priamry tag will also be removed.
> - To set a new or change a primary tag, you should click on the "edit" link adjacent to the primary tag text, and then use the dropdown that appears to make your selection.  Click "OK" to confirm the change, or "cancel" to revert back to the original state.
> - The new primary tag is not saved until you save the draft or update the post

> **Theme**
> - 2 functions are provided for get the stored primary tag
> - function the_primary_tag( {post_id (optional)}, { return (true | false) }),
>> - returns / outputs a simple string of the primary tag name
>> - post_id, optional you can leave blank if in the loop
>> - return, optional, if false will echo out value, if true will return value

> - function the_primary_tag_html( {post_id (optional)}, { return (true | false) }),
>> - return / outputs the name of the primary tag wrapped in an anchor tag to that tag.
>> - post_id, optional you can leave blank if in the loop
>> - return, optional, if false will echo out value, if true will return value
