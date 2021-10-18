

$defaultAssets = call_user_func([$block, "configureAssets"]);

$blockSettings = $datas;

$event = new GenericEvent(null, ['settings' => $blockSettings, "block" => $block, 'assets' => $defaultAssets ]);
