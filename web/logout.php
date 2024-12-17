<?php

session_save_path('/app/web/sessions');
session_start();
session_unset();
session_destroy();
header('Location: /');
