<div data-kt-menu-trigger="click" class="menu-item menu-accordion ">
    <span class="menu-link">
        <span class="menu-icon">
            <i class="fas fas fa-chart-bar fs-3" aria-hidden="true"></i>
        </span>
        <span class="menu-title">Quick Reports</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">

        <?php
        
        MenuItem($link = url('/'), $label = 'POE Analytics');
        // MenuItem($link = url('/ScreeningVolumebyPOE'), $label = 'Screening Traffic');
        
        MenuItem($link = url('/monthlyScreeningVolumeByPOE'), $label = 'Screening Volume');
        MenuItem($link = url('/AlertVolumeByMonth'), $label = 'Alert Volume');
        
        MenuItem($link = url('/HighRiskAlertByMonth'), $label = 'High Risk Alerts');
        
        MenuItem($link = url('/SymptomDiseaseAnalysis'), $label = 'Symptom Analysis');
        MenuItem($link = url('/AgeGenderAnalysis'), $label = 'Gender Analytics');
        MenuItem($link = url('/ageDistribution'), $label = 'Age Analytics');
        MenuItem($link = url('/TravelRouteAnalysis'), $label = 'Travel  Analytics');
        
        MenuItem($link = url('/getSuspectedCasesByProvince'), $label = 'Alerts by Province');
        MenuItem($link = url('/getSuspectedCasesByDistrict'), $label = 'Alerts by District');
        MenuItem($link = url('/getSuspectedCasesByPoe'), $label = 'Alerts by POE');
        // MenuItem($link = url('/casesReport'), $label = 'Travel Route Analytics');
        ?>


    </div>
</div>


<div data-kt-menu-trigger="click" class="menu-item menu-accordion show">
    <span class="menu-link">
        <span class="menu-icon">
            <i class="fas fas fa-chart-line fs-3" aria-hidden="true"></i>
        </span>
        <span class="menu-title">Detailed Reports</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">

        <?php
        
        MenuItem($link = url('/SelectAlertDatabaseDiseases'), $label = 'Alert Database');
        
        MenuItem($link = url('/SelectClassification'), $label = 'Traveller Follow Up');
        
        // MenuItem($link = url('/casesReport'), $label = 'Travel Route Analytics');
        
        ?>


    </div>
</div>


<div data-kt-menu-trigger="click" class="menu-item menu-accordion show">
    <span class="menu-link">
        <span class="menu-icon">
            <i class="fas fas fa-cogs fs-3" aria-hidden="true"></i>
        </span>
        <span class="menu-title">Data Management</span>
        <span class="menu-arrow"></span>
    </span>
    <div class="menu-sub menu-sub-accordion menu-active-bg">

        <?php
        
        MenuItem($link = url('/MgtPoes'), $label = 'Manage POEs');
        
        // MenuItem($link = url('/SelectClassification'), $label = 'Manage Users');
        
        MenuItem($link = url('/index'), $label = 'Manage  Admins');
        
        // MenuItem($link = url('/casesReport'), $label = 'Travel Route Analytics');
        
        ?>


    </div>
</div>
