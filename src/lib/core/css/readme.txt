The cssparser is a small class that enables you to parse css information.
The parsed css information can then be used in your application to fill your needs to use css information.

Clear();
========
Clears the current content. If the html property of the class is set to true then the propertylist is filled with standard html information.

SetHTML($html);
===============
Set how to handle standard html information with clear. Set to true to include html properties and false to exclude it.

Add($key, $codestr);
====================
Add a new propertystring to th list. The key represents under which tag/id/class/subclass to store the information.
The codestr is a string of css properties. Each property should be separated by a ;. Values should be separated from the propertynames by a :.

Get($key, $property);
=====================
Retreive the value of a property.

GetSection($key);
=================
Retreive all properties associated with the given key.

ParseStr($str);
===============
Parse a textstring that contains css information.

Parse($filename);
=================
Parse a file that contains css information.

GetCSS();
=========
Returns a brute style css text compiled of the different properties.