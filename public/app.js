function main () {

    const button = document.createElement('button')

    // enregistrement du service worker si le navigateur supporte les sw et les APIs PushManager et Notification
    if (navigator.serviceWorker && window.PushManager && window.Notification) {
        navigator.serviceWorker.register('/sw.js')
            .then(function(registration) {
                console.log('Service Worker registration successful with scope: ', registration.scope);
            }).catch(function(error) {
            // Enregistrement du service worker a échoué
            console.log('Service Worker registration failed: ', error);
        });
    } else {
        console.log('Service Worker are not supported.');
    }

    // Mise le texte du bouton flottant (clochette) en bah à droite de la page lors du chargement de la page
    updateButtonText();

    // Affichage de la fenêtre modale de gestion des notification (pop-up de permission du navigateur)
    showNotificationModal();

}

const serverKey = 'BL2uSX9-tfxlcYdm157dv-xf_5o7kDo8DfOfHgWUcymTGE6xv5GA_9DwoJdIAOV5JlM8GXR6uAzBIUMjo0fPHMc';
const permission = localStorage.getItem('notificationPermission') || Notification.permission;
const notificationWidget = document.getElementById('notificationWidget');
const notificationManager = document.getElementById('notificationManager');
const toggleNotifications = document.getElementById('toggleNotifications');
const statutNotifications = document.getElementById('statutNotifications');

// Permission demandée à l'utilisateur
async function askPermission() {
    const permission = await Notification.requestPermission(
        function(status) {
            console.log('Statut de la permission de notifications: ', status);
        }
    )
    if (permission === "granted") {
        await registerServiceWorker();
    }
}

// Enregistrement du service worker
async function registerServiceWorker () {
    const registration = await navigator.serviceWorker.register('/sw.js')
    // récupération des abonnements aux notificiations
    let subscription = await registration.pushManager.getSubscription();
    console.log(subscription);
    // Si l'utilisateur n'est pas encore abonné, création d'un nouvel abonnement (subscribe)
    if(!subscription) {
        subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            // signature pour s'authentifier au niveau du serveur push
            applicationServerKey: serverKey,
        })
    } else {
        console.log({subscription});
    }
    await saveSubscription(subscription);
}

// Sauvegarde un abonnement en base de données
async function saveSubscription(subscription) {
    await fetch("/subscribe", {
        method: "post",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
        },
        body: JSON.stringify(subscription),
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error('Erreur lors de l\'enregistrement de l\'abonnement.');
            }
            return response.json();
        })
        .then((data) => {
            console.log(data);
        })
        .catch((error) => {
            console.error(error);
        });
}

// Fonction permettant de détecter le navigateur courant
function getBrowser() {
    const userAgent = navigator.userAgent;

    if (userAgent.indexOf("Firefox") > -1) {
        return "Firefox";
    } else if (userAgent.indexOf("Chrome") > -1) {
        return "Chrome";
    } else if (userAgent.indexOf("Safari") > -1) {
        return "Safari";
    } else if (userAgent.indexOf("Edge") > -1) {
        return "Edge";
    } else {
        return "Unknown";
    }
}

/* Fenêtre modale qui redirige vers la fenêtre askPermission du navigateur */

// Ouvre la fenêtre modale de askPermission
function showModal(modal) {
    modal.classList.add('show');
    modal.style.display = 'block';
    document.body.classList.add('modal-open');
}

// Ferme la fenêtre modale de askPermission
function hideModal(modal) {
    modal.classList.remove('show');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
}

// Fonction d'affichage de la fenêtre modale concernant la permission accordée par l'utilisateur pour la réception de notifications
async function showNotificationModal() {
    // Event de scroll pour détecter quand l'utilisateur commence à faire défiler la page
    await window.addEventListener('scroll', function () {
        // Vérifie si l'utilisateur a fait défiler suffisamment pour afficher la fenêtre modale
        if (window.scrollY > 100) {
            // Vérifie si la fenêtre modale a déjà été affichée ou si l'utilisateur a déjà donné la permission
            if (!localStorage.getItem('notificationAlertShown') && permission !== 'granted' && permission !== 'denied') {
                // Création de la fenêtre modale
                const firstModal = document.createElement('div');
                firstModal.className = 'modal fade';
                firstModal.setAttribute('id', 'notificationModal');
                firstModal.setAttribute('tabindex', '-1');
                firstModal.setAttribute('role', 'dialog');
                firstModal.setAttribute('aria-labelledby', 'notificationModalTitle');
                firstModal.setAttribute('aria-hidden', 'true');
                firstModal.innerHTML =
                    `
                    <div class="modal-dialog modal-dialog-centered" role="document" style="margin-top: -5%;">
                        <div class="modal-content modal-notif" style="background-color: rgb(240,237,230); color: #5e604b">
                            <h3 class="modal-title" id="notificationModalTitle">Suivez vos réservations en temps réel !</h3>
                            <p>Vous souhaitez rester au courant de l'état de votre réservation d'atelier? Pour cela, autorisez la réception de notifications.</p>
                            <button type="button" class="btn btn-primary" style="background-color: #5e604b; color: #f0ede6; border: none" id="manage-notif">Gérer mes notifications</button>
                        </div>
                    </div>
                    `;


                document.body.appendChild(firstModal);

                // Ouverture de la fenêtre
                showModal(firstModal);

                // Event de click sur le bouton "Gérer mes notifications" de la fenêtre modale
                const manageNotifBtn = document.getElementById('manage-notif');
                manageNotifBtn.addEventListener('click', async function () {
                    await askPermission();
                    // Stocke l'info indiquant que l'utilisateur a interagi avec la fenêtre modale
                    localStorage.setItem('managedNotifications', true);
                    // Fermeture de la fenêtre
                    await hideModal(firstModal);
                    updateButtonText();
                });

                // Stocke l'info indiquant que la fenêtre modale a été affichée
                localStorage.setItem('notificationAlertShown', true);
            }
        }
    });

    // Event pour écouter les changements dans le localStorage
    window.addEventListener('storage', function (event) {
        if (event.key === 'notificationPermission' && event.newValue !== 'granted') {
            localStorage.removeItem('managedNotifications');
            localStorage.removeItem('notificationAlertShown');
        }
    });
}


// widget pour gestion autorisation notifications
/* bouton flottant en bas à droite de l'écran qui ouvre un widget permettant à l'utilisateur de modifier sa gestion de notifications à tout moment */
// Event click sur le bouton flottant qui affiche le widget de gestion des notifications
notificationWidget.addEventListener('click', () => {
    if (notificationManager.style.display === 'none') {
        notificationManager.style.display = 'block';
    } else {
        closeWidget();
    }
});

// Ferme le widget
function closeWidget() {
    notificationManager.style.display = 'none';
}

// Mettez à jour le texte du bouton en fonction de l'état de la permission
function updateButtonText() {
    const permission = Notification.permission;
    if (permission === 'granted') {
        toggleNotifications.textContent = 'Bloquer les notifications';
        statutNotifications.innerText = 'autorisé';
    } else {
        toggleNotifications.textContent = 'Autoriser les notifications';
        statutNotifications.innerText = 'bloqué';
    }
}


// Ouverture de la fenêtre modale de modification de la permission pour les notifs
function openPermissionModal(permission) {

    const browser = getBrowser();
    let instructions;

    switch (browser) {
        case "Firefox":
            instructions = `Cliquez sur l'icône <i class="icofont-settings"></i> à gauche de la barre de navigation pour bloquer les notifications. ${permission === "granted" ? "" : "<br>Rafraichissez la page et appuyez de nouveau sur la petite clochette pour autoriser les notifications."}`;
            break;
        case "Chrome":
            instructions = `Cliquez sur l'icone <i class="icofont-unlock"></i> à gauche de la barre de navigation et ${permission === "granted" ? "bloquez" : "autorisez"} les notifications`;
            break;
        case "Safari":
            instructions = `Rendez-vous dans les paramètres de Safari > sites web > notifications > sélectionnez 'Nestor' dans la liste à droite et ${permission === "granted" ? "bloquez" : "autorisez"} les notifications`;
            break;
        case "Edge":
            instructions = `Cliquez sur l'icone <i class="icofont-unlock"></i> à gauche de la barre de navigation et ${permission === "granted" ? "bloquez" : "autorisez"} les notifications`;
            break;
        default:
            instructions = `default`;
    }

    const modal = document.createElement("div");
    modal.className = "modal fade";
    modal.setAttribute("id", "notificationModal");
    modal.setAttribute("tabindex", "-1");
    modal.setAttribute("role", "dialog");
    modal.setAttribute("aria-labelledby", "notificationModalTitle");
    modal.setAttribute("aria-hidden", "true");
    modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered" role="document" style="margin-top: -5%;">
              <div class="modal-content modal-explication-notif" style="background-color: rgb(240,237,230); color: #5E604BFF">
                <div class="modal-header">
                  <h3 class="modal-title" id="notificationModalTitle">Notifications ${permission === "granted" ? "autorisées" : "bloquées"}</h3>
                  <button type="button" class="btn text-end" style="color: #5E604BFF; font-size: 1.3rem" id="btnClose">X</button>
                </div>
                <div class="modal-body">
                    <p style="font-size: 1.1rem; text-align: center">${instructions}</p>
                 </div>
              </div>
            </div>
          `;

    document.body.appendChild(modal);
    showModal(modal);

    // Event de click sur le bouton 'fermer' du modal
    const btnClose = document.getElementById('btnClose');
    btnClose.addEventListener('click',  (e) => {
        hideModal(modal);
    });
}


// Quand l'utilisateur clique sur le bouton, ça ferme le widget
toggleNotifications.addEventListener('click', closeWidget);

// Event au click du bouton "autoriser" ou "bloquer" les notifications
toggleNotifications.addEventListener('click', async () => {
    let permission = Notification.permission;

    if (permission === 'granted') {
        openPermissionModal(permission);

    } else if (permission === 'default') {
        permission = await Notification.requestPermission();
        updateButtonText();
        if (permission === 'granted') {
            // Vérifie si l'utilisateur est abonné aux notifications et l'abonne si ce n'est pas le cas.
            const registration = await navigator.serviceWorker.ready;
            const subscription = await registration.pushManager.getSubscription();
            if (!subscription) {
                const newSubscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: serverKey,
                });
                // Enregistrement de l'abonnement en base de données
                await saveSubscription(newSubscription);
            }
        }
    } else if (permission === 'denied') {
        openPermissionModal(permission);
    }
});


main();