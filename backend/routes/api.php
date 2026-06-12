<?php

foreach (glob(app_path('Modules/*/Routes/*.php')) as $routeFile) {
    require $routeFile;
}
