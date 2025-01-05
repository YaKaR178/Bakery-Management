
<?php
/**
 * File Name: admin.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2024-12-19
 * Last Modified: 2024-12-30
 * Description:
 * This page serves as the admin dashboard for managing various aspects of the e-commerce system. 
 * The admin can perform the following tasks:
 *  - **Catalog**: Add, remove, or edit items in the product catalog.
 *  - **Coupons**: Manage checkout coupons, including adding, removing, or editing coupons.
 *  - **Permissions**: Manage user roles and permissions, including promoting users to admin or revoking admin rights.
 *  - **Orders**: View and manage orders placed by customers, including order status updates.
 * 
 * Each task is linked to a dedicated page where the admin can carry out specific actions such as modifying products, applying discounts, managing users, or reviewing and updating orders.
 * 
 */
?>


<!--Header-->
<?php include 'Style/header.php'; ?>

    <!--Feature-->
    <section id="product1" class="section-p1">
        <h2>Admin Tasks</h2>

        <div class="admin">

            <div class="pro-container" id="pro">

                <div class="pro">
                    <a href="admin_catalog.php" class="fa fa-shopping-basket" id="admin-icon"></a>
                    <div class="admin-des">
                        <h5>Catalog</h5>
                        <span>Add/Remove/Edit Catalog Items</span>
                    </div>
                </div>

            </div>


            <div class="pro-container" id="pro">

                <div class="pro">
                    <a href="admin_coupons.php" class="fa fa-tags" id="admin-icon"></a>
                    <div class="admin-des">
                        <h5>Coupons</h5>
                        <span>Add/Remove/Edit Checkout Coupons</span>
                    </div>
                </div>

            </div>


            <div class="pro-container" id="pro">

                <div class="pro">
                    <a href="admin_permissions.php" class="fa fa-group" id="admin-icon"></a>
                    <div class="admin-des">
                        <h5>Permissions</h5>
                        <span>Manage Permissions for Users</span>
                    </div>
                </div>

            </div>


            <div class="pro-container" id="pro">

                <div class="pro">
                    <a href="admin_orders.php" class="fa fa-credit-card" id="admin-icon"></a>
                    <div class="admin-des">
                        <h5>Orders</h5>
                        <span>Manage Orders</span>
                    </div>
                </div>

            </div>

        </div>
    </section>



<!--Footer-->  
<?php require 'Style/footer.php'; ?>
    

    