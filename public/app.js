function main () {
    const permission = document.getElementById('push-permission')


    const button = document.createElement('button')
    button.innerText = 'Recevoir les notifications'
    permission.appendChild(button)
    button.addEventListener('click', askPermission)
}

const serverKey = 'BL2uSX9-tfxlcYdm157dv-xf_5o7kDo8DfOfHgWUcymTGE6xv5GA_9DwoJdIAOV5JlM8GXR6uAzBIUMjo0fPHMc';

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
    console.log(JSON.stringify(subscription));
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


main();