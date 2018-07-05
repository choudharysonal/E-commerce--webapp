$(document).ready(function(e){
    console.log(e);
    $('.addtocartForm').submit(function(event){
        event.preventDefault();
        console.log("running");
        var formData = {
            'qty': event.currentTarget[0].value,
            'isbn': event.currentTarget[1].value,
            'basketId': event.currentTarget[2].value,
            'bookTitle': event.currentTarget[3].value,
            'stockcount': event.currentTarget[4].value,
            'action': 'addtocart'
        };
        $.ajax({
            type: 'POST',
            url: '../model/shoppingbasket-model.php',
            data: formData,
            dataType: 'json'
        }).done(function(data){
            console.log(data);
            if(data.success){
                // Add a message or visual indication that this card/book is added to cart
                // Update the counter on the shopping basket button
                console.log("success");
                $('.counter').text(data.newcount);
            }
        });
    });

    $('#buyBtn').on('click', function(){
        console.log('buy clicked');
        $.ajax({
            type: 'POST',
            url: '../model/shoppingbasket-model.php',
            data: {action: 'buy'},
            dataType: 'json'
        }).done(function(data){
            console.log(data);
            if(data.success){
                console.log("successful purchase");
                location.href="logout.php";
            }
        });
    });
});