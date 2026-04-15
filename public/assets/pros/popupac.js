function openPopup(event) {
        event.preventDefault(); // Prevent the default behavior of the link

        const accountNo1 = event.target.getAttribute("data-account-no1");
        const accountName1 = event.target.getAttribute("data-account-name1");

        const accountNo2 = event.target.getAttribute("data-account-no2");
        const accountName2 = event.target.getAttribute("data-account-name2");

        const accountNo3 = event.target.getAttribute("data-account-no3");
        const accountName3 = event.target.getAttribute("data-account-name3");

        Swal.fire({
            title: "Account Details",
            html: `
                <p>Account No 1: ${accountNo1}, Account Name 1: ${accountName1}</p>
                <p>Account No 2: ${accountNo2}, Account Name 2: ${accountName2}</p>
                <p>Account No 3: ${accountNo3}, Account Name 3: ${accountName3}</p>
            `,
            icon: "info",
            confirmButtonText: "OK",
        });
    }