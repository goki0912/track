import { defineStore } from "pinia";
import axios from "axios";
import { ref } from "vue";

export const useSpotifyStore = defineStore("spotify", () => {
  const accessToken = ref<string | null>(sessionStorage.getItem("spotify_access_token"));
  const refreshToken = ref<string | null>(localStorage.getItem("spotify_refresh_token"));
  const userProfile = ref<any>(null);

  const setTokens = (access: string, refresh: string) => {
    accessToken.value = access;
    refreshToken.value = refresh;
    sessionStorage.setItem("spotify_access_token", access);
    localStorage.setItem("spotify_refresh_token", refresh);
  };

  const clearTokens = () => {
    accessToken.value = null;
    refreshToken.value = null;
    sessionStorage.removeItem("spotify_access_token");
    localStorage.removeItem("spotify_refresh_token");
  };

  const fetchUserProfile = async () => {
    if (accessToken.value) {
      try {
        const response = await axios.get("/spotify/user-profile", {
          headers: {
            spotifyAuthorization: `Bearer ${accessToken.value}`,
          },
        });
        if (!response.data.error) {
          userProfile.value = response.data;
        }
      } catch (error) {
        console.error("Error fetching user profile:", error);
        throw error; // エラーを再スローする
      }
    }
  };

  const refreshAccessToken = async () => {
    if (refreshToken.value) {
      try {
        const response = await axios.post("/spotify/refresh-token", {
          refresh_token: refreshToken.value,
        });
        setTokens(response.data.access_token, refreshToken.value);
      } catch (error) {
        console.error("Error refreshing access token:", error);
        clearTokens();
      }
    }
  };

  const ensureAuthenticated = async () => {
    // アクセストークンがあるが、それが有効か確認するため、ユーザープロフィールを取得
    if (accessToken.value) {
      try {
        await fetchUserProfile(); // ここで期限切れならエラーが発生
      } catch (error: any) {
        if (error.response && error.response.status === 401) {
          console.log("アクセストークンが無効です。リフレッシュします。");
          await refreshAccessToken(); // 期限切れならリフレッシュトークンで再取得
        } else {
          console.error("エラーが発生しました:", error);
        }
      }
    }
  };

  return {
    accessToken,
    refreshToken,
    userProfile,
    setTokens,
    clearTokens,
    fetchUserProfile,
    refreshAccessToken,
    ensureAuthenticated,
  };
});
