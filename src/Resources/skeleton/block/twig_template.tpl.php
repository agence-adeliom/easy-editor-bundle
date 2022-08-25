<ul>
    <li>Your block at <code><?= $helper->getFileLink(sprintf('%s/%s', $root_directory, $block_path), sprintf('%s', $block_path)); ?></code></li>
    <li>Your template at <code><?= $helper->getFileLink(sprintf('%s/%s', $root_directory, $relative_path), sprintf('%s', $relative_path)); ?></code></li>
</ul>