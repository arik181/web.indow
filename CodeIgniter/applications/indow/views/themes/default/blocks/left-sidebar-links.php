<?
    switch($nav)
    {
    case "dashboard":
        $dashboard_active   = " sidebar_active";
        break;
    case "estimates":
        $estimates_active   = " sidebar_active";
        break;
    case "quotes":
        $quotes_active      = " sidebar_active";
        break;
    case "customers":
        $customers_active   = " sidebar_active";
        break;
    case "sites":
        $sites_active       = " sidebar_active";
        break;
    case "orders":
        $orders_active      = " sidebar_active";
        break;
    case "production":
        $production_active  = " sidebar_active";
        break;
    case "logistics":
        $logistics_active  = " sidebar_active";
        break;
    case "fulfillment_reports":
        $fulfillment_reports_active  = " sidebar_active";
        break;
    /*case "billing":
        $billing_active     = " sidebar_active";
        break;*/
    case "fulfillment":
        $fulfillment_active =  " sidebar_active";
        break;
    case "shipping":
        $shipping_active    = " sidebar_active";
        break;
    case "planning":
        $planning_active    = " sidebar_active";
        break;
    case "users":
        $users_active       = " sidebar_active";
        break;
    case "groups":
        $groups_active      = " sidebar_active";
        break;
    case "permissions":
        $permissions_active = " sidebar_active";
        break;
    case "products":
        $products_active    = " sidebar_active";
        break;
    case "discounts":
        $discounts_active    = " sidebar_active";
        break;
    case "reports":
        $reports_active    = " sidebar_active";
        break;
    default:
        break;
    }
?>

<div id="sidebar_links">
    <ul>
        <li class="sidebar_link sidebar_parent<?= isset($dashboard_active) ? $dashboard_active : ''; ?>"><a href="/dashboard" id="sidebar_dashboard">Dashboard</a></li>

        <? if ($this->permissionslibrary->has_permission(2)) { //see feature ids at top of permissionslibrary for values ?>
        <li class="sidebar_link sidebar_parent<? echo isset($estimates_active) ? $estimates_active : ''; ?>" id="sidebar_estimates"><a href="/estimates">Estimates</a></li>
        <? } ?>

        <? if ($this->permissionslibrary->has_permission(1)) { ?>
        <li class="sidebar_link sidebar_parent<? echo isset($customers_active) ? $customers_active : ''; ?>" id="sidebar_customers"><a href="/customers">Customers</a></li>
        <? } ?>

        <? if ($this->permissionslibrary->has_permission(5)) { ?>
        <li class="sidebar_link sidebar_parent<? echo isset($sites_active) ? $sites_active : ''; ?>"><a href="/sites" id="sidebar_sites">Job Sites</a></li>
        <? } ?>

        <? if ($this->permissionslibrary->has_permission(6)) { ?>
        <li class="sidebar_link sidebar_parent<? echo isset($orders_active) ? $orders_active : ''; ?>"><a href="/orders" id="sidebar_orders">Orders</a></li>
        <? } ?>

        <?php if($this->data['user']->in_admin_group):?>
            <li class="sidebar_link sidebar_parent <? echo isset($fulfillment_active) && 0 ? $fulfillment_active : ''; ?>" id="sidebar_fulfillment"><a href="javascript:void(0)">Fulfillment</a></li>
            <li class="sidebar_link sidebar_child fulfillment_child <? echo isset($fulfillment_active) ? $fulfillment_active : ''; ?>" id="sidebar_fulfillment_dashboard"><a href="/fulfillment">Dashboard</a></li>
            <li class="sidebar_link sidebar_child fulfillment_child <? echo isset($planning_active) ? $planning_active : ''; ?>" id="sidebar_planning"><a href="/planning">Demand Planning</a></li>
            <li class="sidebar_link sidebar_child fulfillment_child <? echo isset($production_active) ? $production_active : ''; ?>" id="sidebar_production"><a href="/production">Production</a></li>
            <li class="sidebar_link sidebar_child fulfillment_child <? echo isset($logistics_active) ? $logistics_active : ''; ?>" id="sidebar_logistics"><a href="/logistics">Logistics</a></li>
            <li class="sidebar_link sidebar_child fulfillment_child <? echo isset($fulfillment_reports_active) ? $fulfillment_reports_active : ''; ?>" id="sidebar_fulfillment_reports"><a href="/fulfillment/reports">Reports</a></li>
            <!--<li class="sidebar_link sidebar_child fulfillment_child<? echo isset($billing_active) ? $billing_active : ''; ?>" id="sidebar_billing"><a href="/billing">Billing</a></li>-->
            <!--<li class="sidebar_link sidebar_parent<? echo isset($planning_active) ? $planning_active : ''; ?>"><a href="/planning" id="sidebar_planning">Demand Planning</a></li>-->
            <li class="sidebar_link sidebar_parent" id="sidebar_users_groups">Users/Groups</li>
            <li class="sidebar_link sidebar_child users_groups_child<? echo isset($users_active) ? $users_active : ''; ?>" id="sidebar_users"><a href="/users">Users</a></li>
            <li class="sidebar_link sidebar_child users_groups_child<? echo isset($groups_active) ? $groups_active : ''; ?>" id="sidebar_groups"><a href="/groups">Groups</a></li>
            <li class="sidebar_link sidebar_child users_groups_child<? echo isset($permissions_active) ? $permissions_active : ''; ?>" id="sidebar_permissions"><a href="/permissions">Permissions</a></li>
            <li class="sidebar_link sidebar_parent<? echo isset($products_active) ? $products_active : ''; ?>"><a href="/products" id="sidebar_products">Products</a></li>
        <?php endif; ?>
        <? if ($this->permissionslibrary->has_permission(13)) { ?>
            <li class="sidebar_link sidebar_parent<? echo isset($discounts_active) ? $discounts_active : ''; ?>"><a href="/discounts" id="sidebar_discounts">Discounts</a></li>
        <? } ?>

        <? if ($this->permissionslibrary->has_permission(11)) { ?>
        <li class="sidebar_link sidebar_parent<? echo isset($reports_active) ? $reports_active : ''; ?>"><a href="/reports" id="sidebar_reports">Reports</a></li>
        <? } ?>
    </ul>
</div>
