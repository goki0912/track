// types/index.ts

// Spotifyのトラック情報の型定義
export interface SpotifyTrack {
    id: string;
    name: string;
    artists: { name: string }[];
    album: {
        name: string;
        images: { url: string }[];
    };
}

// Trackモデルの型定義
export interface Track {
    spotify_track_id: string;
    track_name: string;
    artist_name: string;
    album_name: string;
    album_image_url: string;
}

// Postモデルの型定義
export interface Post {
    id: number;
    user_id: number;
    track_id: number;
    user: {
        name: string;
    };
    track: Track;
    created_at: string;
    updated_at: string;
}
