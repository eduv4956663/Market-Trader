document.addEventListener('click', async (e) => {
    if (!e.target.classList.contains("removeBtn"))
        return;

    const id = e.target.dataset.listingId;

    const res = await fetch("/api/alter_cart.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            listing_id: id,
            action: "remove"
        })
    });

    const data = await res.text();
    console.log(data);


    location.reload();
})