mkdir -p ../../../plugins
mkdir -p ../../../temp
mkdir -p ../../../site/plugins/default/controllers
mkdir -p ../../../site/plugins/default/models
mkdir -p ../../../site/plugins/default/system
mkdir -p ../../../site/plugins/default/views/content/index
mkdir -p ../../../site/settings
mkdir -p ../../../site/system
mkdir -p ../../../site/skins/default/views/template_c
mkdir -p ../../../site/skins/simple/views/template_c
mkdir -p ../../../site/skins/ajax/template_c
mkdir -p ../../../site/skins/default/views/ajax/template_c
mkdir -p ../../../site/skins/default/public/css

cp content.tpl ../../../site/plugins/default/views/content/index/index.tpl

echo "{\$content}" > ../../../site/skins/ajax/index.tpl

cp IndexController.php ../../../site/plugins/default/controllers/IndexController.php
cp model.php ../../../site/plugins/default/models/defaultModel.php
cp error.tpl ../../../site/skins/default/views/error.tpl

cp ../index.php ../../../index.php
cp debug.php ../../../debug.php
cp routes.php ../../../site/plugins/default/system/routes.php
cp ../.htaccess ../../../.htaccess

cp index.tpl ../../../site/skins/default/views/index.tpl
cp simple.tpl ../../../site/skins/simple/views/index.tpl
cp style.css ../../../site/skins/default/public/css/style.css
echo "{\$content}" > ../../../site/skins/default/views/ajax/index.tpl

cp db.php ../../../site/settings/db.php
cp config.php ../../../site/settings/config.php
cp SiteBaseController_install.php ../../../site/system/SiteBaseController.php