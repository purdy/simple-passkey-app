document.addEventListener('DOMContentLoaded', function() {
  // Helper functions.
  var helper = {
    // (A1) ARRAY BUFFER TO BASE 64
    atb: b => {
      let u = new Uint8Array(b), s = "";
      for (let i = 0; i < u.byteLength; i++) { s += String.fromCharCode(u[i]); }
      return btoa(s);
    },

    // (A2) BASE 64 TO ARRAY BUFFER
    bta: o => {
      let pre = "=?BINARY?B?", suf = "?=";
      for (let k in o) {
        if (typeof o[k] == "string") {
          let s = o[k];
          if (s.substring(0, pre.length) == pre && s.substring(s.length - suf.length) == suf) {
            let b = window.atob(s.substring(pre.length, s.length - suf.length)),
              u = new Uint8Array(b.length);
            for (let i = 0; i < b.length; i++) { u[i] = b.charCodeAt(i); }
            o[k] = u.buffer;
          }
        } else { helper.bta(o[k]); }
      }
    }
  };

  // Add event listener to the create passkey button to trigger a call to the backend-signup-pre.php script to create a challenge.
  document.getElementById('create-passkey').addEventListener('click', async (event) => {
    event.preventDefault();
    let userEmail = document.getElementById('email').value;
    let form_data = new FormData();
    form_data.append('email', userEmail);
    let response = await fetch('/backend-signup-pre.php', {
      method: 'POST',
      body: form_data
    });
    let data = await response.json();
    helper.bta(data);
    try {
      let credential = await navigator.credentials.create(data);
      let credential_data = {
        client: credential.response.clientDataJSON ? helper.atb(credential.response.clientDataJSON) : null,
        attest: credential.response.attestationObject ? helper.atb(credential.response.attestationObject) : null
      };
      form_data.append('credential', JSON.stringify(credential_data));
      let response = await fetch('/backend-signup.php', {
        method: 'POST',
        body: form_data
      });
      console.log(response);
    }
    catch (e) {
      // This is when the user cancels the registration or if the registration fails.
      console.log(e);
    }
  });

  document.getElementById('login-btn').addEventListener('click', async (event) => {
    event.preventDefault();
    let response = await fetch('/backend-login-pre.php');
    let data = await response.json();
    helper.bta(data);
    let credential = await navigator.credentials.get(data);
    console.log(data);
    console.log(credential);
    let credential_data = {
      id: credential.rawId ? helper.atb(credential.rawId) : null,
      client: credential.response.clientDataJSON ? helper.atb(credential.response.clientDataJSON) : null,
      auth: credential.response.authenticatorData ? helper.atb(credential.response.authenticatorData) : null,
      sig: credential.response.signature ? helper.atb(credential.response.signature) : null,
      user: credential.response.userHandle ? helper.atb(credential.response.userHandle) : null
    };
    let form_data = new FormData();
    form_data.append('credential', JSON.stringify(credential_data));
    response = await fetch('/backend-login.php', {
      method: 'POST',
      body: form_data
    });
    console.log(response);
    data = await response.json();
    if (data.result == 'success') {
      window.location.href = '/dashboard.php';
    }
  });
});
