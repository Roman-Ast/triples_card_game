export default (msgObject) => {
    console.dir(msgObject);

    $('.playerBetField').each(function () {
        $(this).text('');
    });

    $('.playerBetField').each(function () {
        msgObject.playersCookingOrNot.forEach(item => {
            if (item.name === $(this).attr('ownerName') && item.cooking) {
                $(this).text('варю');
            } else if (item.name === $(this).attr('ownerName') && item.cooking === false) {
                $(this).text('пасс');
            }
        });
    });
    
};