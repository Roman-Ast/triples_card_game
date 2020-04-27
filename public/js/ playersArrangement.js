const playersArrangement = (allPlayers, room, currentPlayerName, currentDistributorName, firstWordPlayer) => {
    const colorForYourContainer = 'red';

    

    if (allPlayers.length == 3) {

        allPlayers.forEach((element, index, array) => {
            if (index === 0) {
                const playerContainer = document.createElement("div");
                playerContainer.classList.add("playerContainer");
                const imgContainer = document.createElement("div");
                const playerDataContainer = document.createElement("div");
                const img = new Image(24, 24);
                img.src = 'https://img.icons8.com/wired/2x/circled-user.png';
                imgContainer.appendChild(img); 
                playerContainer.appendChild(imgContainer);
                playerContainer.appendChild(playerDataContainer);
                if (element.name === currentPlayerName) {
                    playerDataContainer.append("Вы");
                    playerDataContainer.style.color = "#66FF00";
                }
                
                playerContainer.appendChild(playerDataContainer);
                room.append(playerContainer);

                const roomHeight = room.height();
                const playerContainerHeight = $(playerContainer).height();
                
                $(playerContainer)
                    .css({"transform": `translateY(calc(${roomHeight / 2 - playerContainerHeight / 2}px))`});
                    
            } else if (index === 1) {
                const playerContainer = document.createElement("div");
                playerContainer.classList.add("playerContainer");
                const imgContainer = document.createElement("div");
                const playerDataContainer = document.createElement("div");
                const img = new Image(24, 24);
                img.src = 'https://img.icons8.com/wired/2x/circled-user.png';
                imgContainer.appendChild(img); 
                playerContainer.appendChild(imgContainer);
                playerContainer.appendChild(playerDataContainer);
                if (element.name === currentPlayerName) {
                    playerDataContainer.append("Вы");
                    playerDataContainer.style.color = "#66FF00";
                }
                playerContainer.appendChild(playerDataContainer);
                room.append(playerContainer);

                const roomWidth = room.width();
                const playerContainerWidth = $(playerContainer).width();
                
                $(playerContainer)
                    .css({"transform": `translateX(calc(${roomWidth / 2 - playerContainerWidth / 2}px))`});
                    
            } else {
                const playerContainer = document.createElement("div");
                playerContainer.classList.add("playerContainer");
                const imgContainer = document.createElement("div");
                const playerDataContainer = document.createElement("div");
                const img = new Image(24, 24);
                img.src = 'https://img.icons8.com/wired/2x/circled-user.png';
                imgContainer.appendChild(img); 
                playerContainer.appendChild(imgContainer);
                playerContainer.appendChild(playerDataContainer);
                if (element.name === currentPlayerName) {
                    playerDataContainer.append("Вы");
                    playerDataContainer.style.color = "#66FF00";
                }
                playerContainer.appendChild(playerDataContainer);
                room.append(playerContainer);
                
                const roomHeight = room.height();
                const playerContainerHeight = $(playerContainer).height();
                
                $(playerContainer)
                    .css({'right': 0})
                    .css({"transform": `translateY(calc(${roomHeight / 2 - playerContainerHeight / 2}px))`});
            }
        });

    } else if (allPlayers.length === 4) {

        allPlayers.forEach((element, index, array) => {
            if (index === 0) {
                const playerContainer = document.createElement("div");
                $(playerContainer).attr('ownerName', element.name);
                playerContainer.classList.add("playerContainer");
                const imgContainer = document.createElement("div");
                const playerNameContainer = document.createElement("div");
                const playerDataContainer = document.createElement("div");
                const img = new Image(24, 24);
                img.src = 'https://img.icons8.com/wired/2x/circled-user.png';
                imgContainer.appendChild(img); 
                playerContainer.appendChild(imgContainer);
                
                if (element.name === currentPlayerName) {
                    playerNameContainer.append("Вы");
                    playerNameContainer.style.color = colorForYourContainer;
                }
                else playerNameContainer.append(element.name);
                playerContainer.appendChild(playerNameContainer);
                
                $(playerDataContainer)
                    .css({'width':'100%'})
                    .css({'height': '13px'})
                    .css({'display': 'flex'})
                    .css({'flex-direction': 'row'})
                    .css({'justify-content': 'center'})
                    .css({'align-items': 'center'});

                if (element.name === currentDistributorName) {
                    const distributorFlag = document.createElement("div");
                    
                    $(distributorFlag)
                        .css({'width': '5px'})
                        .css({'height': '5px'})
                        .css({'border-radius': '50%'})
                        .css({'background-color': '#800080'});
                    playerDataContainer.appendChild(distributorFlag);
                }

                if (element.name === firstWordPlayer) {
                    const firstWordFlag = document.createElement("div");
                    firstWordFlag.classList.add('firstWordFlag');

                    $(firstWordFlag)
                        .css({'border': '10px solid transparent'})
                        .css({'border-bottom': '10px solid green'})
                    playerDataContainer.appendChild(firstWordFlag);
                }

                playerContainer.appendChild(playerDataContainer);

                $("#room").append(playerContainer);

                const roomHeight = room.height();
                const playerContainerHeight = $(playerContainer).height();
                const roomWidth = room.width();
                const playerContainerWidth = $(playerContainer).width();
                
                $(playerContainer)
                    .css({"left": `calc(${roomWidth / 2 - playerContainerWidth / 2}px)`})
                    .css({"transform": `translateY(calc(${roomHeight - 5 - playerContainerHeight}px))`});
                    

            } else if (index === 1) {

                const playerContainer = document.createElement("div");
                $(playerContainer).attr('ownerName', element.name);
                playerContainer.classList.add("playerContainer");
                const imgContainer = document.createElement("div");
                const playerNameContainer = document.createElement("div");
                const playerDataContainer = document.createElement("div");
                const img = new Image(24, 24);
                img.src = 'https://img.icons8.com/wired/2x/circled-user.png';
                imgContainer.appendChild(img); 
                playerContainer.appendChild(imgContainer);
                
                if (element.name === currentPlayerName) {
                    playerNameContainer.append("Вы");
                    playerNameContainer.style.color = colorForYourContainer;
                }
                else playerNameContainer.append(element.name);
                playerContainer.appendChild(playerNameContainer);
                
                $(playerDataContainer)
                    .css({'width':'100%'})
                    .css({'height': '13px'})
                    .css({'display': 'flex'})
                    .css({'flex-direction': 'row'})
                    .css({'justify-content': 'center'})
                    .css({'align-items': 'center'});

                if (element.name === currentDistributorName) {
                    const distributorFlag = document.createElement("div");
                    
                    $(distributorFlag)
                        .css({'width': '5px'})
                        .css({'height': '5px'})
                        .css({'border-radius': '50%'})
                        .css({'background-color': '#800080'});
                    playerDataContainer.appendChild(distributorFlag);
                }

                if (element.name === firstWordPlayer) {
                    const firstWordFlag = document.createElement("div");
                    firstWordFlag.classList.add('firstWordFlag');
                    
                    $(firstWordFlag)
                        .css({'border': '10px solid transparent'})
                        .css({'border-bottom': '10px solid green'})
                    playerDataContainer.appendChild(firstWordFlag);
                }

                playerContainer.appendChild(playerDataContainer);

                $("#room").append(playerContainer);

                const roomHeight = room.height();
                const playerContainerHeight = $(playerContainer).height();
                
                $(playerContainer)
                    .css({"transform": `translateY(calc(${roomHeight / 2 - playerContainerHeight / 2}px))`});
                    
            } else if (index === 2){
                const playerContainer = document.createElement("div");
                $(playerContainer).attr('ownerName', element.name);
                playerContainer.classList.add("playerContainer");
                const imgContainer = document.createElement("div");
                const playerNameContainer = document.createElement("div");
                const playerDataContainer = document.createElement("div");
                const img = new Image(24, 24);
                img.src = 'https://img.icons8.com/wired/2x/circled-user.png';
                imgContainer.appendChild(img); 
                playerContainer.appendChild(imgContainer);
                
                if (element.name === currentPlayerName) {
                    playerNameContainer.append("Вы");
                    playerNameContainer.style.color = colorForYourContainer;
                }
                else playerNameContainer.append(element.name);
                playerContainer.appendChild(playerNameContainer);
                
                $(playerDataContainer)
                    .css({'width':'100%'})
                    .css({'height': '13px'})
                    .css({'display': 'flex'})
                    .css({'flex-direction': 'row'})
                    .css({'justify-content': 'center'})
                    .css({'align-items': 'center'});

                if (element.name === currentDistributorName) {
                    const distributorFlag = document.createElement("div");
                    
                    $(distributorFlag)
                        .css({'width': '5px'})
                        .css({'height': '5px'})
                        .css({'border-radius': '50%'})
                        .css({'background-color': '#800080'});
                    playerDataContainer.appendChild(distributorFlag);
                }

                if (element.name === firstWordPlayer) {
                    const firstWordFlag = document.createElement("div");
                    firstWordFlag.classList.add('firstWordFlag');
                    
                    $(firstWordFlag)
                        .css({'border': '10px solid transparent'})
                        .css({'border-bottom': '10px solid green'})
                    playerDataContainer.appendChild(firstWordFlag);
                }

                playerContainer.appendChild(playerDataContainer);

                $("#room").append(playerContainer);

                const roomWidth = room.width();
                const playerContainerWidth = $(playerContainer).width();
                
                $(playerContainer)
                    .css({"transform": `translateX(calc(${roomWidth / 2 - playerContainerWidth / 2}px))`});
            } else {
                const playerContainer = document.createElement("div");
                $(playerContainer).attr('ownerName', element.name);
                console.log($(playerContainer).attr('ownerName'));
                playerContainer.classList.add("playerContainer");
                const imgContainer = document.createElement("div");
                const playerNameContainer = document.createElement("div");
                const playerDataContainer = document.createElement("div");
                const img = new Image(24, 24);
                img.src = 'https://img.icons8.com/wired/2x/circled-user.png';
                imgContainer.appendChild(img); 
                playerContainer.appendChild(imgContainer);
                
                if (element.name === currentPlayerName) {
                    playerNameContainer.append("Вы");
                    playerNameContainer.style.color = colorForYourContainer;
                }
                else playerNameContainer.append(element.name);
                playerContainer.appendChild(playerNameContainer);
                
                $(playerDataContainer)
                    .css({'width':'100%'})
                    .css({'height': '13px'})
                    .css({'display': 'flex'})
                    .css({'flex-direction': 'row'})
                    .css({'justify-content': 'center'})
                    .css({'align-items': 'center'});

                if (element.name === currentDistributorName) {
                    const distributorFlag = document.createElement("div");
                    
                    $(distributorFlag)
                        .css({'width': '5px'})
                        .css({'height': '5px'})
                        .css({'border-radius': '50%'})
                        .css({'background-color': '#800080'});
                    playerDataContainer.appendChild(distributorFlag);
                }

                if (element.name === firstWordPlayer) {
                    const firstWordFlag = document.createElement("div");
                    firstWordFlag.classList.add('firstWordFlag');
                    
                    $(firstWordFlag)
                        .css({'border': '10px solid transparent'})
                        .css({'border-bottom': '10px solid green'})
                    playerDataContainer.appendChild(firstWordFlag);
                }

                playerContainer.appendChild(playerDataContainer);

                $("#room").append(playerContainer);
                
                const roomHeight = room.height();
                const playerContainerHeight = $(playerContainer).height();
                
                $(playerContainer)
                    .css({'right': 0})
                    .css({"transform": `translateY(calc(${roomHeight / 2 - playerContainerHeight / 2}px))`});
            }
        });
    }
};

export default playersArrangement;