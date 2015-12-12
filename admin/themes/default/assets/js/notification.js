function notif(note){
            $.notification({
                type:"success",
                width:"400",
                content:"<i class='fa fa-check fa-2x'></i>"+note,
                html:true,
                autoClose:true,
                timeOut:"2000",delay:"0",
                position:"topRight",
                effect:"fade",
                animate:"fadeDown",
                easing:"easeInOutQuad",});
}