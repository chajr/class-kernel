List of events in ClassKernel
====================

## Register events:

### register_get_object_before
Called when try to create simple object. Parameters:

1. **&$name** - name of class to create object
2. **&$args** - arguments for object

### register_get_object_exception
Called when try to create simple object. Parameters:

1. **$name** - name of class to create object
2. **$args** - arguments for object
3. **$e** - exception instance

### register_get_object_after
Called when try to create simple object. Parameters:

1. **$name** - name of class to create object
2. **$args** - arguments for object
3. **$object** - created object instance

### register_get_singleton_before
Called when try to create simple object. Parameters:

1. **&$class** - name of class to create object
2. **&$args** - arguments for object
3. **$instanceName** - optional name of instance to handle singleton

### register_get_singleton_after
Called when try to create simple object. Parameters:

1. **$class** - name of class to create object
2. **$args** - arguments for object
3. **$instanceName** - optional name of instance to handle singleton
4. **$instance** - created or returned singleton instance


### register_set_object_before
Called when try to create simple object. Parameters:

1. **&$class** - name of class to create object
2. **&$name** - name of instance of object
3. **&$args** - arguments for object


### register_set_object_after
Called when try to create simple object. Parameters:

1. **$class** - name of class to create object
2. **$name** - name of instance of object
3. **$args** - arguments for object
4. **$object** - created singleton instance
