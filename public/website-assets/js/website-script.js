$(document).ready(function () {
    //csrf setup
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $("#filterForm").on("change", ".form-check-input", function () {
        fetchProducts($("#filterForm").data("url"));
    });
    //pagination click
    $(document).on("click", "#product-list .pagination a", function (e) {
        e.preventDefault();
        fetchProducts($(this).attr("href"));
    });

    function fetchProducts(url = null) {
        url = url ?? $("#filterForm").data("url");
        $("#product-loader").removeClass("d-none");
        $.ajax({
            url: url,
            type: "get",
            data: $("#filterForm").serialize(),
            success: function (html) {
                $("#product-list").html(html);
                const params = $("#filterForm").serialize();

                let newUrl = url;
                if (params) {
                    newUrl += (url.includes("?") ? "&" : "?") + params;
                }

                window.history.replaceState({}, "", newUrl);
            },
            complete: function () {
                $("#product-loader").addClass("d-none");
            },
        });
    }
    //clear filters
    $("#clearFilters").on("click", function () {
        // Reset form inputs
        $("#filterForm")[0].reset();

        // Fetch products again (page 1)
        fetchProducts($("#filterForm").data("url"));
    });
});
let dangerColor = "#ea1c1c";
// reusable submit
$(document).on("submit", ".ajax-form", function (e) {
    e.preventDefault();
    let form = $(this);
    let url = form.data("url");
    let data = new FormData(form[0]);
    let method = form.data("method") || "POST";
    $.ajax({
        url: url,
        type: method,
        data: data,
        contentType: false,
        processData: false,
        success: function (res) {
            console.log(res);
            if (res.redirect) {
                window.location.href = res.redirect;
            }
            //add to cart
            if (form.data("type") === "productDetail") {
                $("#cartAlert").removeClass("d-none").addClass("show");
                // Update cart badge
                const cartBadge = document.getElementById("cart-count");
                if (cartBadge) cartBadge.innerText = res.data.cart_count;
                return;
            }
            $.toast({
                heading: "Success",
                position: "top-right", // This moves the toast to the top right
                text: res.message,
                icon: "success",
                afterHidden: function () {
                    //$form[0].reset();
                    if (res?.extra?.redirect) {
                        window.location.href = res.extra.redirect;
                    }
                    //reload
                    if (res.reload) {
                        location.reload();
                    }
                },
            });

            if (form.data("type") === "billingUpdate" && res.data) {
                $("#discount-row").show();
                $("#subtotal").text(res.data.subtotal);
                $("#discount").text("- " + res.data.discount);
                $("#tax").text(res.data.tax);
                $("#shipping").text(res.data.shipping);
                $("#grand_total").text(res.data.grand_total);
            }
        },
        error: function (xhr) {
            let res = xhr.responseJSON;

            if (xhr.status === 422) {
                //erors display
            } else if (xhr.status === 400) {
                //console.log('here');
                $.toast({
                    heading: "Error",
                    position: "top-right", // This moves the toast to the top right
                    text: res?.message ?? "Invalid request",
                    showHideTransition: "fade",
                    icon: "error",
                    loaderBg: dangerColor,
                    afterHidden: function () {
                        if (res?.extra?.redirect) {
                            window.location.href = res.extra.redirect;
                        }
                    },
                });
            } else {
                $.toast({
                    heading: "Error",
                    position: "top-right", // This moves the toast to the top right
                    text: res.message,
                    showHideTransition: "fade",
                    icon: "error",
                    loaderBg: dangerColor,
                });
            }
        },
    });
});
//check input exceed stock cart
/*$('.quantity__plus').on('click', function (e) {
    e.preventDefault();
    let input = $(this).siblings(".quantity__input");
    let max = parseInt(input.attr('max'));
    let val = parseInt(input.val());
    let plusBtn = $(this);

    if (val >= max) {
        plusBtn.addClass('disabled');
        plusBtn.css('pointer-events', 'none');

        $.toast({
            heading: "Stock limit",
            text: "Only " + max + " items available",
            icon: "warning"
        });
        input.val(val);
        return
    }
    // input.val(val + 1);
})*/
$(document).on("click", ".quantity__plus", function (e) {
    const updateUrl = $("#product-time").data("update-url");
    e.preventDefault();

    let wrapper = $(this).closest(".quantity");
    let input = wrapper.find(".quantity__input");

    let qty = parseInt(input.val());
    let max = parseInt(input.attr("max"));

    if (qty >= max) {
        $.toast({
            heading: "Stock limit",
            position: "top-right", // This moves the toast to the top right
            text: "Only " + max + " items available",
            icon: "warning",
        });
        return;
    }

    qty++;
    input.val(qty);

    // 🔍 check context
    if (wrapper.data("context") === "cart") {
        updateCartQty(wrapper.data("item-id"), qty, updateUrl);
    }
});
//enable plus when decrease quantity by user
$(document).on("click", ".quantity__minus", function (e) {
    const updateUrl = $("#product-time").data("update-url");
    e.preventDefault();

    let wrapper = $(this).closest(".quantity");
    let input = wrapper.find(".quantity__input");
    let plusBtn = wrapper.find(".quantity__plus");

    let min = parseInt(input.attr("min"), 10) || 1;
    let qty = parseInt(input.val(), 10) || 1;

    if (qty <= min) return;

    // input.val(val - 1);
    qty--;
    input.val(qty);
    // re-enable plus button
    plusBtn.removeClass("disabled");
    plusBtn.css("pointer-events", "auto");
    if (wrapper.data("context") === "cart") {
        updateCartQty(wrapper.data("item-id"), qty, updateUrl);
    }
});
//update quantity at cart page (quantity increase or decrease)
function updateCartQty(CartItemId, qty, updateUrl) {
    $.ajax({
        url: updateUrl,
        type: "POST",
        data: {
            cart_item_id: CartItemId,
            quantity: qty,
        },
        success: function (res) {
console.log(res);
             $("#item-total-" + CartItemId).text(res.item_total);
    $("#subtotal").text(res.subtotal);
    $("#discount").text("- " + res.discount);
    $("#tax").text(res.tax);
    $("#shipping").text(res.shipping);
    $("#grand_total").text(res.grand_total);
    const cartBadge = document.getElementById("cart-count");
    if (cartBadge) cartBadge.innerText = res.cart_count;
        },
        error: function (xhr) {
             let res = xhr.responseJSON;
          if (xhr.status === 422) {
                //erors display
            } else if (xhr.status === 400) {
                //console.log('here');
                $.toast({
                    heading: "Error",
                    position: "top-right", // This moves the toast to the top right
                    text: res?.message ?? "Invalid request",
                    showHideTransition: "fade",
                    icon: "error",
                    loaderBg: dangerColor,
                    afterHidden: function () {
                        if (res?.extra?.redirect) {
                            window.location.href = res.extra.redirect;
                        }
                    },
                });
            } else {
                $.toast({
                    heading: "Error",
                    position: "top-right", // This moves the toast to the top right
                    text: res.message,
                    showHideTransition: "fade",
                    icon: "error",
                    loaderBg: dangerColor,
                });
            }
        },
    });
}
//remove item in cart page
function removeCartItem(CartItemId, removeUrl) {
    $.ajax({
        url: removeUrl,
        data: {
            cart_item_id: CartItemId,
        },
        type: "POST",
        success: function (res) {
            $("#cart-row-" + CartItemId).remove();
            $("#subtotal").text(res.data.subtotal);
            $("#discount").text("- " + res.data.discount);
            $("#tax").text(res.data.tax);
            $("#shipping").text(res.data.shipping);
            $("#grand_total").text(res.data.grand_total);
            // Update cart badge
            const cartBadge = document.getElementById("cart-count");
            if (cartBadge) cartBadge.innerText = res.data.cart_count;
        },
    });
}
$(document).on("click", ".remove-cart-item", function (e) {
    e.preventDefault();
    const removeUrl = $("#product-time").data("remove-url");
    let cartItem = $(this).data("id");
    removeCartItem(cartItem, removeUrl);
});

document.addEventListener("click", function (e) {
    const btn = e.target.closest(".wishlist-toggle");
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation();
    const icon = btn.querySelector("i");

    const productId = btn.dataset.product;
    const urlApp = btn.dataset.url;
    fetch(`${urlApp}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            Accept: "application/json",
            "Content-Type": "application/json",
        },
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.action === "added") {
                icon.classList.remove("fa-regular");
                icon.classList.add("fa-solid");
            } else {
                icon.classList.remove("fa-solid");
                icon.classList.add("fa-regular");
            }

            const countEl = document.getElementById("wishlist-count");
            if (countEl) countEl.innerText = data.count;
        });
});

document.addEventListener("click", function (e) {
    const btn = e.target.closest(".wishlist-remove");
    if (!btn) return;

    e.preventDefault();

    const url = btn.dataset.url;
    const card = btn.closest(".wishlist-item");

    fetch(url, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            Accept: "application/json",
        },
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.action === "removed") {
                // Smooth fade out
                card.style.transition = "0.3s";
                card.style.opacity = "0";
                card.style.transform = "scale(0.95)";

                setTimeout(() => {
                    card.remove();
                }, 300);

                // Update wishlist count if exists
                const countEl = document.getElementById("wishlist-count");
                if (countEl) countEl.innerText = data.count;
            }
        });
});

document.addEventListener("DOMContentLoaded", function () {

    const checkoutForm = document.getElementById("checkoutForm");
    const externalBtn = document.getElementById("externalSubmitBtn");

    const sameAsShipping = document.getElementById("sameAsShipping");

    const shippingWrapper = document.getElementById("shipping-fields-wrapper");
    const billingWrapper = document.getElementById("billing-fields-wrapper");

    const toggleNewAddressBtn = document.getElementById("toggleNewAddress");
    const manualBillingForm = document.getElementById("manual-billing-form");
    const savedAddressContainer = document.getElementById("saved-addresses-container");

    const showSavedBtn = document.getElementById("showSavedAddress");

    /* =====================================================
       UTILITIES
    ====================================================== */

    function setSectionState(container, { required, disabled }) {
        if (!container) return;
        const fields = container.querySelectorAll("input, select, textarea");
        fields.forEach((field) => {
            field.disabled = disabled;
            if (required) {
                field.setAttribute("data-required", "true");
            } else {
                field.removeAttribute("data-required");
            }
        });
    }

    function clearErrors(container) {
        if (!container) return;
        const errors = container.querySelectorAll(".field-error");
        errors.forEach((el) => el.remove());
    }

    function showFieldError(field, message) {
        const error = document.createElement("div");
        error.className = "text-danger small field-error mt-1";
        error.innerText = message;
        field.closest(".mb-3, .col-4, .col-6, .col-8")?.appendChild(error);
    }

    function validateSection(container) {
        if (!container) return true;
        clearErrors(container);
        let valid = true;
        const requiredFields = container.querySelectorAll('[data-required="true"]');
        requiredFields.forEach((field) => {
            if (!field.value.trim()) {
                showFieldError(field, "Este campo es obligatorio");
                if (valid) field.focus();
                valid = false;
            }
        });
        return valid;
    }
    if (showSavedBtn) {
    showSavedBtn.addEventListener("click", function () {
        activateSavedBilling();
    });
}


    /* =====================================================
       BILLING STATE MACHINE (Saved vs Manual)
    ====================================================== */

    function activateManualBilling() {
        manualBillingForm.classList.remove("d-none");
        setSectionState(manualBillingForm, {
            required: true,
            disabled: false,
        });
        if (savedAddressContainer) {
            const radios = savedAddressContainer.querySelectorAll('input[type="radio"]');
            radios.forEach((r) => {
                //r.checked = false;
                r.disabled = true;
            });
              savedAddressContainer.classList.add("d-none");
              showSavedBtn?.classList.remove("d-none");
        }
        toggleNewAddressBtn.innerText = "× Usar dirección guardada";
        toggleNewAddressBtn.dataset.mode = "manual";
    }

    function activateSavedBilling() {
        manualBillingForm.classList.add("d-none");
        setSectionState(manualBillingForm, {
            required: false,
            disabled: true,
        });
        clearErrors(manualBillingForm);
        if (savedAddressContainer) {
            const radios = savedAddressContainer.querySelectorAll('input[type="radio"]');
            radios.forEach((r) => (r.disabled = false));
             savedAddressContainer.classList.remove("d-none");
               showSavedBtn?.classList.add("d-none");
        }
        toggleNewAddressBtn.innerText = "+ Agregar una nueva dirección";
        toggleNewAddressBtn.dataset.mode = "saved";

    }

    if (toggleNewAddressBtn) {
        toggleNewAddressBtn.dataset.mode = "saved";
        toggleNewAddressBtn.addEventListener("click", function () {
            if (this.dataset.mode === "saved") {
                activateManualBilling();
            } else {
                activateSavedBilling();
            }
        });

    }

    /* =====================================================
       BILLING SAME AS SHIPPING
    ====================================================== */

    function updateBillingState() {
        if (!sameAsShipping) return;
        if (sameAsShipping.checked) {
            billingWrapper.classList.add("d-none");
            setSectionState(billingWrapper, {
                required: false,
                disabled: true,
            });
            clearErrors(billingWrapper);
        } else {
            billingWrapper.classList.remove("d-none");
            setSectionState(billingWrapper, {
                required: true,
                disabled: false,
            });
        }

    }

    if (sameAsShipping) {
        sameAsShipping.addEventListener("change", updateBillingState);
        updateBillingState();
    }

    /* =====================================================
       FINAL VALIDATION
    ====================================================== */

    if (externalBtn) {
        externalBtn.addEventListener("click", function () {
            let billingValid = true;
            let shippingValid = true;
            /* SHIPPING ALWAYS REQUIRED */
            shippingValid = validateSection(shippingWrapper);
            /* BILLING VALIDATION ONLY IF DIFFERENT */
            if (!sameAsShipping?.checked) {
                const manualActive = toggleNewAddressBtn?.dataset.mode === "manual";
                if (manualActive) {
                    billingValid = validateSection(manualBillingForm);
                } else {
                    const selectedSaved = document.querySelector('input[name="saved_billing_id"]:checked');
                    billingValid = !!selectedSaved;
                    if (!billingValid && savedAddressContainer) {
                        const container = savedAddressContainer;
                        clearErrors(container);
                        const error = document.createElement("div");
                        error.className = "text-danger small field-error mt-2";
                        error.innerText = "Selecciona una dirección guardada";
                        container.appendChild(error);
                    }
                }
            }

            /* SHOW ACCORDION IF ERROR */

            if (!shippingValid) {
                new bootstrap.Collapse(
                    document.getElementById("shippingCollapse"),
                    { show: true }
                );
                return;
            }

            if (!billingValid) {
                new bootstrap.Collapse(
                    document.getElementById("billingCollapse"),
                    { show: true }
                );
                return;
            }
const savedAddresses = document.querySelectorAll(
    'input[name="saved_billing_id"]'
);

const manualVisible =
    !document.getElementById("manual-billing-form")
        .classList.contains("d-none");

if (savedAddresses.length === 0 && !manualVisible) {
    alert("Please add an address first");
    return;
}

            checkoutForm.requestSubmit();
        });

    }

});
