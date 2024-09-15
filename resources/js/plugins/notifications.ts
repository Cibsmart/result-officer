import { TYPE as NotificationType, useToast } from "vue-toastification";
import { router, usePage } from "@inertiajs/vue3";

const toast = useToast();

export const notifications = () => {
  router.on("finish", () => {
    const notification = usePage().props.notification;

    usePage().props.notification = null;

    if (notification) {
      toast(notification.body, { type: notification.type as NotificationType });
    }
  });
};
