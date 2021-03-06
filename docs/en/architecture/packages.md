# Limb3 packages
## Basic conceptions
The conception of package in Limb3 is very primitive. Packaging is achieved through work of special FileResolvers that try to find files in different folders. [See FileResolvers documentation page for more info](./file_resolvers.md).

Limb3 package conception is very raw. There is no means to automate package dependency check so be carefull.

The list of installed packages can be found in PROJECT_DIR/settings/packages.ini. Here is an example of packages.ini file:

    packages[] = {lmb_SHARED_PACKAGES_DIR}/common/branches/trunk/
    packages[] = {lmb_SHARED_PACKAGES_DIR}/simple_acl/branches/trunk/
    packages[] = {lmb_SHARED_PACKAGES_DIR}/service_node/branches/trunk/
    packages[] = {lmb_LOCAL_PACKAGES_DIR}/text_page/branches/trunk/

The directory structure of a package have to match one in Limb core. Otherwise file resolvers will not be able find files in packages.

You must have setup.php file you package root directory. $PACKAGE_NAME variable have to be defined in package setup.php since Limb will define PACKAGE_NAME_DIR constant using $PACKAGE_NAME variable. Here is an example of package setup.php file:

    $PACKAGE_NAME = 'lmb_SERVICE_NODE';
 
    require_once(dirname(__FILE__) . '/ServiceNodePackageToolkit.class.php');
 
    $service_node_toolkit = new ServiceNodePackageToolkit();
 
    Limb :: registerToolkit($service_node_toolkit, 'service_node_toolkit');

As the result ServiceNodePackageToolkit will be registered in Limb and lmb_SERVICE_NODE_DIR is defined. We recommend to use this constant anytime you write PHP include() and require() functions even inside you package.
