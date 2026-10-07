document.addEventListener("alpine:init", () => {
  Alpine.data("login", () => ({
    user: "",
    password: "",
    sending: false,
    error_text: "",
    success_text: "",

    handleSubmit(event) {
      event.preventDefault();

      if (this.sending) {
        return;
      }

      this.sending = true;
      this.error_text = "";
      this.success_text = "";

      fetch("/Centecp_Intranet/backend/public/index.php/api/auth/login", {
        method: "post",
        body: JSON.stringify({
          usu: this.user,
          pas: this.password,
        }),
        headers: {
          "Content-Type": "application/json",
        },
      })
        .then((response) => response.text())
        .then((data) => {
          if (String(data) === "1") {
            this.success_text = "Redireccionado...";
            location.href = "/Centecp_Intranet/frontend/";
          } else {
            this.error_text = "Credenciales incorrectas.";
          }
        })
        .catch((_) => {
          this.error_text = "Credenciales incorrectas.";
        })
        .finally((_) => (this.sending = false));
    },
  }));
});
