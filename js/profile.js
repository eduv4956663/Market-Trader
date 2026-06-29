document.getElementById("logoutBtn").addEventListener("click", async (e) => {
    const res = await fetch("/api/logout.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            action: "logout"
        })
    });

    const data = await res.json();

    if (data.error !== "None") {
        console.log(data.error);
    } else {
        location.reload();
    }
});