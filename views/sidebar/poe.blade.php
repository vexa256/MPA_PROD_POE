<div data-kt-menu-trigger="click" class="menu-item menu-accordion show">
    <span class="menu-link">
        <span class="menu-icon">
            <i class="fas fas fa-keyboard fs-3" aria-hidden="true"></i>
        </span>
        <span class="menu-title">Quick Reports</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">

        <?php
        
        MenuItem($link = url('/'), $label = 'POE Analytics');
        // MenuItem($link = url('/ScreeningVolumebyPOE'), $label = 'Screening Traffic');
        MenuItem($link = url('/getSuspectedCasesByProvince'), $label = 'Alerts by Province');
        MenuItem($link = url('/getSuspectedCasesByDistrict'), $label = 'Alerts by District');
        MenuItem($link = url('/getSuspectedCasesByPoe'), $label = 'Alerts by POE');
        // MenuItem($link = url('/getSuspectedCasesByProvince'), $label = 'Alert Database');
        MenuItem($link = url('/GenderAndRouteAnalysisDashboard'), $label = 'Gender Analytics');
        MenuItem($link = url('/casesReport'), $label = 'Travel Route Analytics');
        ?>


    </div>
</div>


<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
    <span class="menu-link">
        <span class="menu-icon">
            <i class="fas fas fa-keyboard fs-3" aria-hidden="true"></i>
        </span>
        <span class="menu-title">Detailed Reports</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">

        <?php
        
        MenuItem($link = url('/SelectAlertDatabaseDiseases'), $label = 'Alert Database');
        
        // MenuItem($link = url('/casesReport'), $label = 'Travel Route Analytics');
        
        ?>


    </div>
</div>
