
function filterProducts(categoryId) {
    const allProducts = document.querySelectorAll('.product-item');
    allProducts.forEach(product => {
        if (categoryId === 'all') {
            product.style.display = 'block';
        } else {
            const productCategoryId = product.dataset.categoryId;
            if (productCategoryId === categoryId) {
                product.style.display = 'block';
            } else {
                product.style.display = 'none';
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const categoryLinks = document.querySelectorAll('.category-link');
    categoryLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault();
            const categoryId = this.id.split('-')[1];
            filterProducts(categoryId);
        });
    });
});

