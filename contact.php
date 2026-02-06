<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us</title>
  <link rel="stylesheet" href="styles/contact.css" />
</head>

<body>
  <div class="navbar">
    <div class="navbar_container">
      <div class="brand">
        <img src="images/logo.png" alt="logo" />
      </div>
      <div class="menu">
        <a class="nav-item" href="index.php">Home</a>
        <a class="nav-item" href="destination.php">Destination</a>
        <a class="nav-item" href="about.php">About</a>
        <a class="nav-item active" href="#">Contact</a>
        <a class="nav-item" href="visa.php">Visa</a>
        <a class="nav-item" href="transport.php">Transport</a>
        <a class="nav-item" href="tiket.php">Tiket</a>
      </div>
    </div>
  </div>

  <div class="contact">
    <div class="contact_image">
      <h1>Get in Touch</h1>
      <p>We'd love to hear from you. Whether you have a question about our services or just want to say hello, we are here to help.</p>
    </div>
    <div class="contact_form">
      <div class="form-container">
        <h2>Contact Us</h2>
        <p>If you have questions, please feel free to contact us.</p>
        <form id="contactForm" action="" method="post">
          <div class="input-group">
            <label>Username</label>
            <input type="text" placeholder="Enter your username" required />
          </div>
          <div class="input-group">
            <label>Email</label>
            <input type="email" placeholder="Enter your email" required />
          </div>
          <div class="input-group">
            <label>Message</label>
            <textarea rows="4" placeholder="Enter your message" required></textarea>
          </div>
          <div>
            <button type="submit">Submit</button>
          </div>
        </form>
        <div class="success-message" id="successMessage">
          Your message has been sent successfully!
        </div>
      </div>
    </div>
  </div>

  <script>
    document
      .getElementById("contactForm")
      .addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent default form submission

        // Show the success message
        document.getElementById("successMessage").style.display = "block";

        // Optionally, reset the form after a delay
        setTimeout(function () {
          document.getElementById("contactForm").reset();
          document.getElementById("successMessage").style.display = "none";
        }, 3000);
      });
  </script>
</body>

</html>
