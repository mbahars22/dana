</div> <!-- Tutup div content -->
    </div> <!-- Tutup div d-flex -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById("toggleSidebar").addEventListener("click", function() {
        var sidebar = document.getElementById("sidebar");
        var content = document.getElementById("mainContent");
        
        if (sidebar.style.marginLeft === "-250px") {
            sidebar.style.marginLeft = "0";
            content.style.marginLeft = "260px"; 
        } else {
            sidebar.style.marginLeft = "-250px";
            content.style.marginLeft = "10px"; 
        }
    });
</script>

</body>
</html>
