<?php
// footer.php
?><footer>
  <ul class="footer-links">
    <li><a href="#">Enlace 1</a></li>
    <li><a href="#">Enlace 2</a></li>
    <li><a href="#">Enlace 3</a></li>
    <li><a href="#">Enlace 4</a></li>
  </ul>
</footer>
<?php
// Cierra .container y body/html
?>
  </div> <!-- .container -->
  <script>
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    menuToggle.addEventListener('click', () => {
      sidebar.classList.toggle('open');
    });
  </script>
</body>
</html>
