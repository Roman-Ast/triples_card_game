const showCard = (msgObject) => {
    console.log(msgObject);

    $('.playerContainer').each(function () {
        console.log($(this).attr('ownerName'));
       if ($(this).attr('ownerName') === msgObject.showingPLayer) {
            const showingCardContainer = document.createElement('div');
            showingCardContainer.id = 'showingCardContainer';
            const img = new Image(48.2, 72.3);
            img.src = msgObject.showingCard;
            showingCardContainer.appendChild(img);
            showingCardContainer.style.position = 'absolute';
            const top = $(this).offset().top;
            if (top < 80) {
                showingCardContainer.style.bottom = '-60px';
                $(this).append(showingCardContainer);
            } else {
                showingCardContainer.style.bottom = '50px';
                $(this).append(showingCardContainer);
            }
            
       } 
    });

    setTimeout(() => {
        $('#showingCardContainer').detach();
    }, 4000);
};

export default showCard;