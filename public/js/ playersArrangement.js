//функция для создания поля на столе для отображения ставок игрока
const createPlayerBetField = (elementName) => {
    const playerBetField = document.createElement('div');
    playerBetField.classList.add('playerBetField');
    $(playerBetField).attr('ownerName', elementName);

    $(playerBetField)
        .css({'position': 'absolute'})
        .css({'width': '26px'})
        .css({'height': '20px'});

    return playerBetField;
};
//функция для создания контейнера игрока
const createPlayerContainer = (elementName, currentPlayerName, currentDistributorName, firstWordPlayer) => {
    const playerContainer = document.createElement("div");
    playerContainer.classList.add("playerContainer");
    const imgContainer = document.createElement("div");
    imgContainer.classList.add('imgContainer');
    const playerNameContainer = document.createElement("div");
    playerNameContainer.classList.add('playerNameContainer');
    const playerDataContainer = document.createElement("div");
    const img = new Image(36, 28);
    img.src = '/images/table/Shirts.png';
    imgContainer.appendChild(img); 
    playerContainer.appendChild(imgContainer);

    $(playerContainer).attr('ownerName', elementName);
    if (elementName === currentPlayerName) {
        playerNameContainer.append("Вы");
    }
    else playerNameContainer.append(elementName);
    playerContainer.appendChild(playerNameContainer);
                
    $(playerDataContainer)
        .css({'width':'100%'})
        .css({'height': '13px'})
        .css({'display': 'flex'})
        .css({'flex-direction': 'row'})
        .css({'justify-content': 'center'})
        .css({'align-items': 'center'});

    playerDataContainer.classList.add('playerDataContainer');

    if (elementName === currentDistributorName) {
        const distributorFlag = document.createElement("div");
                    
        $(distributorFlag)
            .css({'width': '10px'})
            .css({'height': '10px'})
            .css({'border-radius': '50%'})
            .css({'background-color': '#800080'});
        playerDataContainer.appendChild(distributorFlag);
    }

    if (elementName === firstWordPlayer) {
        const firstWordFlag = document.createElement("div");
        firstWordFlag.classList.add('firstWordFlag');

        $(firstWordFlag)
            .css({'border': '10px solid transparent'})
            .css({'border-bottom': '10px solid #FFC107'})
        playerDataContainer.appendChild(firstWordFlag);
    }

    playerContainer.appendChild(playerDataContainer);

    return playerContainer;
};

const playersArrangement = (
                    allPlayers,
                    room,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                            ) => {

    const roomHeight = room.height();
    const roomWidth = room.width();

    const tableWidth = $('#table').width();
    const tableHeight = $('#table').height();

    if (allPlayers.length === 3) {
        
        allPlayers.forEach((element, index, array) => {
            if (index === 0) {
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerHeight = $(playerContainer).height();
                
                $(playerContainer)
                    .css({"transform": `translateY(calc(${roomHeight / 2 - playerContainerHeight / 2}px))`});
                
                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);

                $(playerBetField)
                    .css({'left': `calc(${roomWidth / 2 - tableWidth / 2}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 2 - $(playerBetField).height() / 2}px))`});

            } else if (index === 1) {
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerWidth = $(playerContainer).width();
                
                $(playerContainer)
                    .css({"transform": `translateX(calc(${roomWidth / 2 - playerContainerWidth / 2}px))`});
                    
                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);

                $(playerBetField)
                    .css({'left': `calc(${roomWidth / 2 - $(playerBetField).width() / 2}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 2 - tableHeight / 2}px))`});
            } else {
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerHeight = $(playerContainer).height();
                
                $(playerContainer)
                    .css({'right': 0})
                    .css({"transform": `translateY(calc(${roomHeight / 2 - playerContainerHeight / 2}px))`})
            
                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);

                $(playerBetField)
                    .css({'left': `calc(${roomWidth / 2 + tableWidth / 2 - $(playerBetField).width()}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 2 - $(playerBetField).height() / 2}px))`});
            
            }
        });

    } else if (allPlayers.length === 4) {
        
        allPlayers.forEach((element, index, array) => {
            if (index === 0) {
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerHeight = $(playerContainer).height();
                const playerContainerWidth = $(playerContainer).width();
                
                $(playerContainer)
                    .css({"left": `calc(${roomWidth / 2 - playerContainerWidth / 2}px)`})
                    .css({"transform": `translateY(calc(${roomHeight - 5 - playerContainerHeight}px))`});
                    
                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);
 
                $(playerBetField)
                    .css({'left': `calc(${roomWidth / 2 - $(playerBetField).width() / 2}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 2 + tableHeight / 2 - $(playerBetField).height()}px))`});
            } else if (index === 1) {

                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerHeight = $(playerContainer).height();
                
                $(playerContainer)
                    .css({"transform": `translateY(calc(${roomHeight / 2 - playerContainerHeight / 2}px))`});
                
                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);
                
                $(playerBetField)
                    .css({'left': `calc(${roomWidth / 2 - tableWidth / 2}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 2 - $(playerBetField).height() / 2}px))`});
                    
            } else if (index === 2){
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerWidth = $(playerContainer).width();
                
                $(playerContainer)
                    .css({"transform": `translateX(calc(${roomWidth / 2 - playerContainerWidth / 2}px))`});
                
                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);
                
                $(playerBetField)
                    .css({'left': `calc(${roomWidth / 2 - $(playerBetField).width() / 2}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 2 - tableHeight / 2}px))`});
            } else {
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerHeight = $(playerContainer).height();
                
                $(playerContainer)
                    .css({'right': 0})
                    .css({"transform": `translateY(calc(${roomHeight / 2 - playerContainerHeight / 2}px))`});

                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);
                
                $(playerBetField)
                    .css({'left': `calc(${roomWidth / 2 + tableWidth / 2 - $(playerBetField).width()}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 2 - $(playerBetField).height() / 2}px))`});
            }
        });
    } else if (allPlayers.length === 5) {
        
        allPlayers.forEach((element, index, array) => {
            if (index === 0) {
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerHeight = $(playerContainer).height();
                const playerContainerWidth = $(playerContainer).width();
                
                $(playerContainer)
                    .css({'left': `calc(${roomWidth / 4 - $(playerContainer).width() / 2}px)`})
                    .css({"transform": `translateY(calc(${roomHeight - ($(playerContainer).height() + 10)}px))`});
                    
                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);
                
                $(playerBetField)
                    .css({'right': `calc(${roomWidth / 2 + tableWidth / 4 - $(playerBetField).width()}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 2 + $(playerBetField).height() * 3}px))`});
            } else if (index === 1) {

                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerHeight = $(playerContainer).height();
                
                $(playerContainer)
                    .css({"transform": `translateY(calc(${roomHeight / 2 - playerContainerHeight / 2}px))`});
                
                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);
                
                $(playerBetField)
                    .css({'left': `calc(${roomWidth / 2 - tableWidth / 2}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 2 - $(playerBetField).height() / 2}px))`});
                    
            } else if (index === 2){
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerWidth = $(playerContainer).width();
                
                $(playerContainer)
                    .css({"transform": `translateX(calc(${roomWidth / 2 - playerContainerWidth / 2}px))`});
                
                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);

                $(playerBetField)
                    .css({'left': `calc(${roomWidth / 2 - $(playerBetField).width() / 2}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 2 - tableHeight / 2}px))`});
            } else if (index === 3) {
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerHeight = $(playerContainer).height();
                
                $(playerContainer)
                    .css({'right': 0})
                    .css({"transform": `translateY(calc(${roomHeight / 2 - playerContainerHeight / 2}px))`});

                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);
                
                $(playerBetField)
                    .css({'left': `calc(${roomWidth / 2 + tableWidth / 2 - $(playerBetField).width()}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 2 - $(playerBetField).height() / 2}px))`});
            } else {
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerHeight = $(playerContainer).height();
                
                $(playerContainer)
                    .css({'right': `calc(${roomWidth / 4 - $(playerContainer).width() / 2}px)`})
                    .css({"transform": `translateY(calc(${roomHeight - ($(playerContainer).height() + 10)}px))`});

                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);

                $(playerBetField)
                    .css({'left': `calc(${roomWidth / 2 + tableWidth / 4 - $(playerBetField).width()}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 2 + $(playerBetField).height() * 3}px))`});
            }
        });
    } else if (allPlayers.length === 6) {
        
        allPlayers.forEach((element, index, array) => {
            if (index === 0) {
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerHeight = $(playerContainer).height();
                const playerContainerWidth = $(playerContainer).width();
                
                $(playerContainer)
                    .css({'left': `calc(${roomWidth / 4 - $(playerContainer).width() / 2}px)`})
                    .css({"transform": `translateY(calc(${roomHeight - ($(playerContainer).height() * 1.5)}px))`});
                    
                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);

                $(playerBetField)
                    .css({'right': `calc(${roomWidth / 2 + tableWidth / 4 - $(playerBetField).width()}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 2 + $(playerBetField).height() * 3}px))`});
            } else if (index === 1) {

                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerHeight = $(playerContainer).height();
                
                $(playerContainer)
                    .css({"transform": `translateY(calc(${roomHeight / 2 - playerContainerHeight / 2}px))`});
                
                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);

                $(playerBetField)
                    .css({'left': `calc(${roomWidth / 2 - tableWidth / 2}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 2 - $(playerBetField).height() / 2}px))`});
                    
            } else if (index === 2){
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerWidth = $(playerContainer).width();
                
                $(playerContainer)
                    .css({'left': `calc(${roomWidth / 4 - $(playerContainer).width() / 2}px)`})
                    .css({'top': `calc(${roomHeight / 4 - $(playerContainer).height()}px)`});
                
                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);

                $(playerBetField)
                    .css({'left': `calc(${roomWidth / 4 + $(playerBetField).width()}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 4 + $(playerBetField).height()}px))`});
            } else if (index === 3) {
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerHeight = $(playerContainer).height();
                
                $(playerContainer)
                    .css({'right': `calc(${roomWidth / 4 - $(playerContainer).width() / 2}px)`})
                    .css({'top': `calc(${roomHeight / 4 - $(playerContainer).height()}px)`});

                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);

                $(playerBetField)
                    .css({'right': `calc(${roomWidth / 4 + $(playerBetField).width()}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 4 + $(playerBetField).height()}px))`});
            } else if (index === 4){
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerHeight = $(playerContainer).height();
                
                $(playerContainer)
                    .css({"transform": `translateY(calc(${roomHeight / 2 - playerContainerHeight / 2}px))`})
                    .css({'right': 0});

                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);

                $(playerBetField)
                    .css({'right': `calc(${roomWidth / 2 - tableWidth / 2}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 2 - $(playerBetField).height() / 2}px))`});;
            } else {
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerHeight = $(playerContainer).height();
                
                $(playerContainer)
                    .css({'left': `calc(${roomWidth / 2 + tableWidth / 2 - $(playerContainer).width()}px)`})
                    .css({"transform": `translateY(calc(${roomHeight - ($(playerContainer).height() * 1.5)}px))`});

                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);

                $(playerBetField)
                    .css({'left': `calc(${roomWidth / 2 + tableWidth / 4 - $(playerBetField).width()}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 2 + $(playerBetField).height() * 3}px))`});
            }
        });
    } else if (allPlayers.length === 7) {
        
        allPlayers.forEach((element, index, array) => {
            if (index === 0) {
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerHeight = $(playerContainer).height();
                const playerContainerWidth = $(playerContainer).width();
                
                $(playerContainer)
                    .css({"left": `calc(${roomWidth / 2 - playerContainerWidth / 2}px)`})
                    .css({"transform": `translateY(calc(${roomHeight - playerContainerHeight * 1.2}px))`});
                    
                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);

                $(playerBetField)
                    .css({'left': `calc(${roomWidth / 2 - $(playerBetField).width() / 2}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 2 + tableHeight / 2 - $(playerBetField).height() * 2}px))`});
            } else if (index === 1) {

                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerHeight = $(playerContainer).height();
                
                $(playerContainer)
                    .css({'left': `calc(${roomWidth / 4 - $(playerContainer).width() / 2}px)`})
                    .css({"transform": `translateY(calc(${roomHeight - ($(playerContainer).height() * 1.5)}px))`});
                
                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);

                $(playerBetField)
                    .css({'right': `calc(${roomWidth / 2 + tableWidth / 3 - $(playerBetField).width()}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 2 + $(playerBetField).height() * 2.5}px))`});
                    
            } else if (index === 2){
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerWidth = $(playerContainer).width();
                const playerContainerHeight = $(playerContainer).height();

                $(playerContainer)
                    .css({"transform": `translateY(calc(${roomHeight / 2 - playerContainerHeight / 2}px))`})
                    .css({'left': 0});
                
                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);

                $(playerBetField)
                .css({'left': `calc(${roomWidth / 2 - tableWidth / 2}px)`})
                .css({"transform": `translateY(calc(${roomHeight / 2 - $(playerBetField).height() / 2}px))`});
            } else if (index === 3) {
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);
                
                $(playerContainer)
                    .css({'left': `calc(${roomWidth / 4 - $(playerContainer).width() / 2.5}px)`})
                    .css({'top': `calc(${roomHeight / 4 - $(playerContainer).height()}px)`});

                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);

                $(playerBetField)
                    .css({'left': `calc(${roomWidth / 4 + $(playerBetField).width()}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 4 + $(playerBetField).height() / 2}px))`});
            } else if (index === 4){
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerHeight = $(playerContainer).height();
                
                $(playerContainer)
                    .css({'right': `calc(${roomWidth / 4 - $(playerContainer).width() / 2}px)`})
                    .css({'top': `calc(${roomHeight / 4 - $(playerContainer).height()}px)`});

                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);

                $(playerBetField)
                    .css({'right': `calc(${roomWidth / 4 + $(playerBetField).width()}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 4 + $(playerBetField).height() / 2}px))`});
            } else if (index === 5) {
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerHeight = $(playerContainer).height();
                
                $(playerContainer)
                    .css({"transform": `translateY(calc(${roomHeight / 2 - playerContainerHeight / 2}px))`})
                    .css({'right': 0 });

                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);
;
                $(playerBetField)
                    .css({'right': `calc(${roomWidth / 2 - tableWidth / 2}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 2 - $(playerBetField).height() / 2}px))`});
                
            } else {
                const playerContainer = createPlayerContainer(
                    element.name,
                    currentPlayerName,
                    currentDistributorName,
                    firstWordPlayer
                );

                room.append(playerContainer);

                const playerContainerHeight = $(playerContainer).height();
                const playerContainerWidth = $(playerContainer).width();
                
                $(playerContainer)
                    .css({'right': `calc(${roomWidth / 4 - $(playerContainer).width() / 2}px)`})
                    .css({"transform": `translateY(calc(${roomHeight - ($(playerContainer).height() * 1.5)}px))`});
                    
                //добавлем поле ставки на стол
                const playerBetField = createPlayerBetField(element.name);
                room.append(playerBetField);

                $(playerBetField)
                    .css({'left': `calc(${roomWidth / 2 + tableWidth / 3 - $(playerBetField).width()}px)`})
                    .css({"transform": `translateY(calc(${roomHeight / 2 + $(playerBetField).height() * 2.5}px))`});
            }
        });
    }
};

export default playersArrangement;