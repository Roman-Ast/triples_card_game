export default (msgObject) => {
    console.dir(msgObject);

    $('.playerBetField').each(function () {
       if (!msgObject.cookingPlayers.includes($(this).attr('ownerName'))) {
        $(this).text('пасс');
       }
    });
};