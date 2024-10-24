<div data-kt-menu-trigger="click" class="menu-item menu-accordion show">
    <span class="menu-link">
        <span class="menu-icon">
            <i class="fas fas fa-keyboard fs-3" aria-hidden="true"></i>
        </span>
        <span class="menu-title">File Report</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">

        <?php
        
        MenuItem($link = route('MgtReportingEntities'), $label = 'Your Reports');
        MenuItem($link = route('ReporterSelectEntity'), $label = 'Submit Reporting');
        
        ?>


    </div>
</div>












<div data-kt-menu-trigger="click" class="menu-item menu-accordion show">
    <span class="menu-link">
        <span class="menu-icon">
            <i class="fas fas fa-chart-line fs-3" aria-hidden="true"></i>
        </span>
        <span class="menu-title">Reports and Analytics</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">

        <?php
        
        MenuItem($link = route('MgtReportingEntities'), $label = 'Reporting Dashboard');
        
        MenuItem($link = route('SelectIndicatorEntity'), $label = 'Country Analytics');
        
        MenuItem($link = route('SelectIndicatorEntity'), $label = 'Regional Analytics');
        
        ?>


    </div>
</div>

<div data-kt-menu-trigger="click" class="menu-item menu-accordion ">
    <span class="menu-link">
        <span class="menu-icon">
            <i class="fas fas fa-cogs fs-3" aria-hidden="true"></i>
        </span>
        <span class="menu-title">Reporting Settings</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">

        <?php
        
        MenuItem($link = route('MgtTimelines'), $label = 'Reporting Timeframes');
        
        ?>


    </div>
</div>

<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
    <span class="menu-link">
        <span class="menu-icon">
            <i class="fas fa-users fs-3" aria-hidden="true"></i>
        </span>
        <span class="menu-title">Reporting Accounts</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">

        <?php
        
        MenuItem($link = route('MgtReportingAccounts'), $label = 'Entity Accounts');
        MenuItem($link = route('MgtAdmins'), $label = 'Admin Accounts');
        
        ?>


    </div>
</div>









<div data-kt-menu-trigger="click" class="menu-item menu-accordion ">
    <span class="menu-link">
        <span class="menu-icon">
            <i class="fas fa-project-diagram fs-3" aria-hidden="true"></i>
        </span>
        <span class="menu-title">Enties and Indicators</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">

        <?php
        
        MenuItem($link = route('MgtReportingEntities'), $label = 'Reporting Entities');
        
        MenuItem($link = route('SelectIndicatorEntity'), $label = 'Entity Indicators');
        
        MenuItem($link = route('RRF'), $label = 'Regional Indicators');
        
        ?>


    </div>
</div>
