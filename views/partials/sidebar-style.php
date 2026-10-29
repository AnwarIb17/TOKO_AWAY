<?php
/* Sidebar positioning (tidak memakai utility translate agar mudah di-override) */
?>
<style>
    #sidebar { transform: translateX(-100%); }
    #sidebar.open { transform: translateX(0); }
    @media (min-width: 1024px) { #sidebar { transform: none; } }
</style>
