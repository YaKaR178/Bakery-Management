<?php
/**
 * File Name: contact.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2024-12-19
 * Last Modified: 2025-01-01
 * Description:
 * This page serves as the **Contact Us** interface for "Baking from the Heart," enabling users to reach out via multiple channels.
 * 
 * Features:
 * - **Hero Section**: Welcomes users with a header and a brief introduction to the contact page.
 * - **Contact Details**: Displays essential information, including:
 *   - Address
 *   - Email
 *   - Phone number
 *   - Operating hours
 *   All elements are styled with corresponding icons for enhanced visual appeal.
 * - **Embedded Google Map**: Allows users to locate the bakery's address directly via an interactive map.
 * - **Contact Form**:
 *   - Users can send messages by filling out their name, email, subject, and message.
 *   - The form submission is directed to `mail.php` for server-side handling.
 * - **WhatsApp Integration**: Offers a quick messaging option via WhatsApp, powered by Elfsight.
 * - **Newsletter Subscription**: Promotes user engagement through a newsletter section.
 * - **Footer and Header**: Ensures consistent branding and navigation with reusable components.
 */

?>
<!--Header-->
<?php include_once 'Style/header.php'; ?>

    <!--Hero-->
    <section id="page-header">
        <h1>Let's Talk!</h1>
        <h4 id="save">We'd love to hear from you</h4>
        <p>Leave us a message, or message us on WhatsApp</p>
    </section>

    <!--Get in Touch-->
    <section id="contact-details" class="section-p1">
        <div class="details">
            <span>Get in Touch</span>
            <h2>Please see our contact info below</h2>
            <h3>Baking from the Heart</h3>
            <div>
                <li>
                    <i class='far fa-map'></i>
                    <p>69 Bakery Rd., Qiryat Ono</p>
                </li>

                <li>
                    <i class='far fa-envelope'></i>
                    <p>bfth_bakery@gmail.com</p>
                </li>

                <li>
                    <i class='fas fa-phone'></i>
                    <p>054-836-3238</p>
                </li>

                <li>
                    <i class="fa fa-clock-o"></i>
                    <p>Su-Th: 09:00-21:00</p>
                </li>
            </div>
        </div>

        <div class="map">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d733.3008501249902!2d34.86454006410179!3d32.05395576414481!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e1!3m2!1sen!2sil!4v1732555620469!5m2!1sen!2sil"
                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>


    <!--Form-->
    <section id="form-details">
        <form action="mail.php" method="POST">
            <h4>Leave a Message</h4>
            <h2>Let us know what's on your mind</h2>
            <input type="text" placeholder="Your Name" name="name">
            <input type="text" placeholder="Email" name="email">
            <input type="text" placeholder="Subject" name="subject">
            <textarea id="" cols="30" rows="10" placeholder="Your Message" name="body"></textarea>
            <button class="normal">Submit</button>
        </form>

        <!--WhatsApp-->
        <div class="chatbot">
            <h4>Need a quick, human reply?</h4>
            <h2>Talk to us on WhatsApp</h2>
            <div class="elfsight-app-72c9a8b3-2985-4d72-bb6a-e6820cfc1fc4" data-elfsight-app-lazy></div>
        </div>
    </section>

    <!--Newsletter-->
    <?php include 'Style/newsletter.php'; ?>

    <!--Footer-->
    <?php include 'Style/footer.php'; ?>
    
    <script src="https://static.elfsight.com/platform/platform.js" async></script>
    <script src="send_mail.js"></script>
    <script src="contact.js"></script>
</body>
