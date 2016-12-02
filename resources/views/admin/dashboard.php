<?php if (Auth::check()) : ?>
    <p>Welcome, <strong><?= Auth::user()->name ?></strong></p>
<?php endif ?>