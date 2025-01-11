// src/components/CreatePost.js
import React, { useState } from "react";
import { createPost } from "../services/api";

const CreatePost = () => {
  const [content, setContent] = useState("");
  const [message, setMessage] = useState("");

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const result = await createPost(content);
      setMessage("Post created successfully!");
      console.log(result); // Do something with the result, like updating the UI
      setContent(""); // Clear the input
    } catch (error) {
      setMessage("Failed to create post.");
    }
  };

  return (
    <div>
      <form onSubmit={handleSubmit}>
        <textarea
          value={content}
          onChange={(e) => setContent(e.target.value)}
          placeholder="Write something..."
          rows="4"
          cols="50"
        ></textarea>
        <br />
        <button type="submit">Post</button>
      </form>
      {message && <p>{message}</p>}
    </div>
  );
};

export default CreatePost;
