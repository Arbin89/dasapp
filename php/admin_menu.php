<div class="admin-dropdown">
    <button type="button" class="admin-dropdown-btn" onclick="document.getElementById('admin-dropdown-menu').classList.toggle('mostrar')">
        Panel Admin ▾
    </button>
    <div id="admin-dropdown-menu" class="admin-dropdown-menu">
        <a href="<?php echo $rutaBase ?? ''; ?>oficina.php">Oficina</a>
        <a href="<?php echo $rutaBase ?? ''; ?>admin_contratistas.php">Contratistas</a>
    </div>
</div>