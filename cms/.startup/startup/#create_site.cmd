mkdir ..\..\..\plugins
mkdir ..\..\..\temp
mkdir ..\..\..\site\plugins\default\controllers
mkdir ..\..\..\site\plugins\default\models
mkdir ..\..\..\site\plugins\default\system
mkdir ..\..\..\site\plugins\default\views\content\index
mkdir ..\..\..\site\settings
mkdir ..\..\..\site\system
mkdir ..\..\..\site\skins\default\views\template_c
mkdir ..\..\..\site\skins\ajax\template_c
mkdir ..\..\..\site\skins\default\views\ajax\template_c
mkdir ..\..\..\site\skins\default\public\css
mkdir ..\..\..\site\skins\simple\views\template_c
mkdir ..\..\..\site\skins\simple\views\ajax\template_c



copy content.tpl ..\..\..\site\plugins\default\views\content\index\index.tpl
copy simple.tpl ..\..\..\site\skins\simple\views\index.tpl

echo {$content} > ..\..\..\site\skins\ajax\index.tpl
echo {$content} > ..\..\..\site\skins\simple\views\ajax\index.tpl

copy IndexController.php ..\..\..\site\plugins\default\controllers\IndexController.php
copy model.php ..\..\..\site\plugins\default\models\defaultModel.php
copy error.tpl ..\..\..\site\skins\default\views\error.tpl

copy ..\index.php ..\..\..\index.php
copy debug.php ..\..\..\debug.php
copy routes.php ..\..\..\site\plugins\default\system\routes.php
copy ..\.htaccess ..\..\..\.htaccess

copy index.tpl ..\..\..\site\skins\default\views\index.tpl
copy style.css ..\..\..\site\skins\default\public\css\style.css
echo {$content} > ..\..\..\site\skins\default\views\ajax\index.tpl

copy db.php ..\..\..\site\settings\db.php
copy config.php ..\..\..\site\settings\config.php
copy SiteBaseController_install.php ..\..\..\site\system\SiteBaseController.php