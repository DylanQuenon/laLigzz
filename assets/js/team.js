const addImage = document.querySelector('#add-image')
addImage.addEventListener('click',()=>{
    // compter combien j'ai d form-group pour les indices ex: annonce_image_0_url
    const widgetCounter = document.querySelector("#widgets-counter")
    const index = +widgetCounter.value // le + permet de transformer en nombre, value rends tjrs un string 
    const annonceImages = document.querySelector("#team_images")
    // recup le prototype dans la div

    const prototype = annonceImages.dataset.prototype.replace(/__name__/g, index) // drapeau g pour indiqur que l'on va le faire plusieurs fois 
    annonceImages.insertAdjacentHTML('beforeend', prototype)
    widgetCounter.value = index+1
    handleDeleteButtons() //  pour mettre à jour la table deletes
})

const updateCounter = () => {
    const count = document.querySelectorAll("#team_images div.form-group").length
    document.querySelector("#widgets-counter").value = count 
}

const handleDeleteButtons = () => {
    let deletes = document.querySelectorAll("button[data-action='delete']")
    deletes.forEach(button => {
        button.addEventListener('click', ()=>{
            const target = button.dataset.target
            const elementTarget = document.querySelector(target)
            if(elementTarget){
                elementTarget.remove() // supprimer l'éléménet
            }
        })
    })

}

updateCounter()
handleDeleteButtons()