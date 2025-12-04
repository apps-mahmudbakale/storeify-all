<?php
$db = mysqli_connect('localhost', 'mahmudbakale', 'root', 'store');
$search_keyword = $_GET['search_keyword'];

$query = mysqli_query($db, "SELECT products.*, station_products.quantity as stock_quantity 
    FROM station_products 
    INNER JOIN products ON station_products.product_id = products.id 
    WHERE (products.name LIKE '%$search_keyword%' OR products.product_category LIKE '%$search_keyword%')
    AND station_products.quantity > 0");
?>
<ul class="nav flex-column">
<?php while($row = mysqli_fetch_array($query)){ ?>
    <li class="nav-item">
        <a href="#" class="nav-link add-to-cart" 
           data-id="<?php echo $row['id']; ?>" 
           data-name="<?php echo htmlspecialchars($row['name']); ?>" 
           data-price="<?php echo $row['selling_price']; ?>"
           data-category="<?php echo htmlspecialchars($row['product_category']); ?>">
            <strong><?php echo $row['name']; ?></strong>
            <small class="d-block text-muted"><?php echo $row['product_category']; ?></small>
            <span class="float-right badge bg-primary">&#8358; <?php echo number_format($row['selling_price'], 2); ?></span>
        </a>
    </li>
<?php } ?>
</ul>

<script>
$(document).ready(function() {
    $('.add-to-cart').click(function(e) {
        e.preventDefault();
        var productId = $(this).data('id');
        var productName = $(this).data('name');
        var productPrice = $(this).data('price');
        var productCategory = $(this).data('category');
        
        // Add to cart logic here
        addToCart(productId, productName, productPrice, productCategory);
    });
    
    function addToCart(productId, productName, productPrice, productCategory) {
        // Your existing add to cart logic
        // Make sure to include productCategory when adding to the cart
        $.ajax({
            url: '/add-to-cart', // Update this URL to your actual endpoint
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                name: productName,
                price: productPrice,
                category: productCategory,
                quantity: 1
            },
            success: function(response) {
                // Handle success - maybe refresh the cart
                location.reload();
            },
            error: function(xhr) {
                console.error('Error adding to cart:', xhr.responseText);
                alert('Error adding product to cart. Please try again.');
            }
        });
    }
});
</script>