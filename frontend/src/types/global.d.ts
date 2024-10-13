// echoとpusherにエラーが出るので無理やり型指定
// https://qiita.com/morohoshi/items/f52b5a93f11ea2ec78e6
interface Window {
    Echo: any;
    Pusher: any;
}
declare let window: Window;
