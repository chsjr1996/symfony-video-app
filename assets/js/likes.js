$(document).ready(function () {
    $('.userLikesVideo').show();
    $('.userDoesNotLikeVideo').show();
    $('.noActionYet').show();

    function changeLikesOrDislikesNumber(target, action, videoId) {
        var numberOfLikesOrDislikesSelector = $('.number-of-' + target + '-' + videoId);
        var numberOfLikesOrDislikes = parseInt(numberOfLikesOrDislikesSelector.html().replace(/\D/g, ''));
        action == 'increase' && numberOfLikesOrDislikes++;
        action == 'decrease' && numberOfLikesOrDislikes--;
        numberOfLikesOrDislikesSelector.html('(' + numberOfLikesOrDislikes + ')');
    }

    function changeLikeOrDislikesVisibility(target, videoId) {
        if (target == 'none') {
            $('.video-id-' + videoId).show();
            $('.likes-video-id-' + videoId).hide();
            $('.dislikes-video-id-' + videoId).hide();
        } else if (target == 'like') {
            $('.video-id-' + videoId).hide();
            $('.likes-video-id-' + videoId).show();
            $('.dislikes-video-id-' + videoId).hide();
        } else if (target == 'dislike') {
            $('.video-id-' + videoId).hide();
            $('.likes-video-id-' + videoId).hide();
            $('.dislikes-video-id-' + videoId).show();
        }
    }

    $('.toggle-likes').on('click', function (e) {
        e.preventDefault();
        var link = $(e.currentTarget).attr('href');

        $.ajax({
            method: 'POST',
            url: link
        }).done(function (data) {
            switch (data.action) {
                case 'liked':
                    changeLikesOrDislikesNumber('likes', 'increase', data.id);
                    changeLikeOrDislikesVisibility('like', data.id);
                    break;
                case 'disliked':
                    changeLikesOrDislikesNumber('dislikes', 'increase', data.id);
                    changeLikeOrDislikesVisibility('dislike', data.id);
                    break;
                case 'undo liked':
                    changeLikesOrDislikesNumber('likes', 'decrease', data.id);
                    changeLikeOrDislikesVisibility('none', data.id);
                    break;
                case 'undo disliked':
                    changeLikesOrDislikesNumber('dislikes', 'decrease', data.id);
                    changeLikeOrDislikesVisibility('none', data.id);
                    break;
            }
        });
    });
});
