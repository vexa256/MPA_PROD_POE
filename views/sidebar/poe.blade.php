<div data-kt-menu-trigger="click" class="menu-item menu-accordion show">
    <span class="menu-link">
        <span class="menu-icon">
            <i class="fas fas fa-keyboard fs-3" aria-hidden="true"></i>
        </span>
        <span class="menu-title">Dashboards</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">

        <?php
        
        MenuItem($link = url('/'), $label = 'POE Analytics');
        MenuItem($link = url('/'), $label = 'Pimary Screening Analytics');
        
        ?>


    </div>
</div>
