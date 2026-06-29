
document.getElementById("addToCart").addEventListener("click", async (e) => {
    const params = new URLSearchParams(window.location.search);
    const listingID = params.get("id");

    const res = await fetch("/api/alter_cart.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            action: "add",
            listing_id: `${listingID}`
        })
    });

    const data = await res.text();

    console.log(data);

    const dispElement = document.getElementById("notify");

    if (data.error !== "None") {
        dispElement.style.color = 'red';
        dispElement.textContent = data.error;
        dispElement.hidden = false;
    } else {
        dispElement.style.color = 'green';
        dispElement.textContent = data.response;
        dispElement.hidden = false;
    }
});

